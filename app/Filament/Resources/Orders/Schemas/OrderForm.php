<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enum\Order\HandlingMethod;
use App\Enum\Order\TypeArise;
use App\Enum\Order\TypeVehicle;
use App\Models\Service;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderForm
{
    public static function baseComponents(): array
    {
        return [
            Fieldset::make('1. Thông tin chung')
                ->schema([
                    Select::make('customer_id')
                        ->label('🙋‍♂️ Khách hàng')
                        ->native(false)
                        ->searchable(['name', 'email'])
                        ->placeholder('Chọn khách hàng...')
                        ->preload()
                        ->relationship('customer', 'name')
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->email}")
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('🙋‍♂️ Họ và tên')
                                ->required(),
                            TextInput::make('email')
                                ->label('📞 Số điện thoại')
                                ->required(),

                        ])
                        ->createOptionAction(function ($action) {
                            return $action
                                ->modalHeading('Thêm khách hàng mới')
                                ->modalDescription('Nhập thông tin khách hàng mới để thêm vào danh sách.')
                                ->modalIcon('heroicon-o-user-plus');
                        })
                        ->createOptionUsing(function (array $data) {
                            $randomPassword = Str::random(10);

                            return User::create([
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'password' => $randomPassword,
                                'role' => 'customer',
                                'status' => 'active',
                            ]);
                        })
                        ->required(),
                    Select::make('staff_id')
                        ->label('Nhân viên chốt đơn')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->default(Auth::user()->id)
                        ->options(User::pluck('name', 'id')),
                ]),

        ];
    }

    public static function tripSection(): Section
    {
        return Section::make('Thông tin đơn hàng')
            ->collapsible()
            ->relationship('tripInfo')
            ->schema([
                Fieldset::make('2. Thông tin điểm lên')
                    ->schema([
                        DatePicker::make('pick_up_date')
                            ->default(now()->startOfDay())
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->native(false)
                            ->label('📅 Ngày lên hàng, lấy hàng')
                            ->placeholder('Chọn ngày lên hàng')
                            ->required()
                            ->reactive(),

                        TimePicker::make('pick_up_time')
                            ->label('⏰ Giờ lên hàng, lấy hàng')
                            ->placeholder('Chọn hoặc nhập giờ')
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->datalist(getTimeData()),
                        TextInput::make('sender_name')
                            ->label('📞 Liên hệ lên hàng, lấy hàng')
                            ->placeholder('Ví dụ: 034534543 - MR Tuấn'),
                        TextInput::make('pick_up_point')
                            ->label('📍 Địa chỉ lên hàng, lấy hàng')
                            ->placeholder('Nhập địa chỉ lấy hàng')
                            ->required(),
                        Select::make('pick_up_method')
                            ->label('🚚 Hình thức lên hàng, lấy hàng')
                            ->options(HandlingMethod::class)
                            ->selectablePlaceholder(true)
                            ->native(false)
                            ->default(HandlingMethod::None)
                            ->searchable()
                            ->placeholder('Chọn hình thức lên hàng'),
                        TextInput::make('pick_up_link')
                            ->label('🔗 Link vị trí lên hàng, lấy hàng')
                            ->placeholder('Dán link Google Maps nếu có')
                            ->url()
                            ->suffixIcon('heroicon-o-link'),
                    ]),
                Fieldset::make('3. Thông tin điểm xuống')
                    ->schema([
                        DatePicker::make('delivery_date')
                            ->default(now()->startOfDay())
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->native(false)
                            ->label('📅 Ngày xuống hàng')
                            ->placeholder('Chọn ngày xuống hàng')
                            ->required()
                            ->reactive(),

                        TimePicker::make('delivery_time')
                            ->label('⏰ Giờ xuống hàng')
                            ->placeholder('Chọn hoặc nhập giờ')
                            ->format('H:i')
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->datalist(getTimeData()),
                        TextInput::make('receiver_name')
                            ->label('📞 Liên hệ xuống hàng')
                            ->placeholder('Ví dụ: 034534543 - MR Tuấn'),
                        TextInput::make('delivery_point')
                            ->label('📍 Địa chỉ xuống hàng')
                            ->placeholder('Nhập địa chỉ lấy hàng')
                            ->required(),
                        Select::make('delivery_method')
                            ->label('🚚 Hình thức xuống hàng')
                            ->options(HandlingMethod::class)
                            ->selectablePlaceholder(true)
                            ->default(HandlingMethod::None)
                            ->native(false)
                            ->searchable()
                            ->placeholder('Chọn hình thức xuống hàng'),
                        TextInput::make('delivery_link')
                            ->label('🔗 Link vị trí xuống hàng')
                            ->placeholder('Dán link Google Maps nếu có')
                            ->url()
                            ->suffixIcon('heroicon-o-link'),
                    ]),
                Fieldset::make('3. Thông tin chuyến')
                    ->schema([
                        Select::make('type_vehicle')
                            ->label('🚚 Loại xe')
                            ->options(TypeVehicle::class)
                            ->default(TypeVehicle::Truck)
                            ->selectablePlaceholder(true)
                            ->native(false)
                            ->placeholder('Chọn loại xe vận chuyển'),

                        TextInput::make('item_name')
                            ->label('Loại hàng')
                            ->required(),
                        TextInput::make('weight')
                            ->label('Trọng lượng (tấn)')
                            ->numeric()
                            ->required(),
                        Select::make('type_arise')
                            ->label('🚚 Loại bốc xếp')
                            ->options(TypeArise::class)
                            ->selectablePlaceholder(true)
                            ->native(false)
                            ->placeholder('Chọn loại bốc xếp'),
                        //
                        TextInput::make('distance')
                            ->label('Khoảng cách (KM)')
                            ->numeric()
                            ->required(fn (callable $get) => $get('has_bo') == false) // chỉ required khi không bo
                            ->readOnly(fn (callable $get) => $get('has_bo') == true)  // khi bo => readonly
                            ->default(0) // giá trị mặc định (nếu cần)
                            ->reactive()
                            ->afterStateHydrated(function ($component, $state, callable $set, callable $get) {
                                // Khi form được load lại (vd: edit record)
                                if ($get('has_bo') == true) {
                                    $set('distance', 0);
                                }
                            }),
                        TextInput::make('point')
                            ->label('Số rớt điểm')
                            ->numeric()
                        // ->visible(fn (callable $get) => $get('has_bo') == true) // 👈 chỉ hiện khi có bo
                            ->required(fn (callable $get) => $get('has_bo') == true),
                        ToggleButtons::make('has_bo')
                            ->label('Đơn hàng bo')
                            ->default(false) // 👈 mặc định false
                            ->inlineLabel(false)
                            ->inline(true)
                            ->required()
                            ->options([
                                true => 'Có',
                                false => 'Không',
                            ])
                            ->colors([
                                true => 'success',
                                false => 'gray',
                            ])
                            ->icons([
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            ])
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Khi user chuyển sang "Có" → set distance = 0
                                if ($state == true) {
                                    $set('distance', 0);
                                }
                            }), // 👈 quan trọng: để trigger visible() và rule() thay đổi theo state
                        ToggleButtons::make('has_cash')
                            ->label('Tài xế thu tiền mặt')
                            ->inlineLabel(false)
                            ->inline(true)
                            ->required()
                            ->options([
                                true => 'Có',
                                false => 'Không',
                            ])
                            ->colors([
                                true => 'success',
                                false => 'gray',
                            ])
                            ->icons([
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            ])
                            ->reactive(),
                        ToggleButtons::make('has_back')
                            ->label('Đơn hàng quay đầu')
                            ->default(false) // 👈 mặc định false
                            ->inlineLabel(false)
                            ->inline(true)
                            ->required()
                            ->options([
                                true => 'Có',
                                false => 'Không',
                            ])
                            ->colors([
                                true => 'success',
                                false => 'gray',
                            ])
                            ->icons([
                                true => 'heroicon-o-check-circle',
                                false => 'heroicon-o-x-circle',
                            ])
                            ->reactive(),
                        TextInput::make('back_point')
                            ->label('Điểm quay đầu')
                            ->visible(fn (callable $get) => $get('has_back') == true),

                    ]),
            ]);
    }

    public static function serviceSection(): Section
    {
        return Section::make('Dịch vụ')
            ->collapsible()
            ->schema([
                Repeater::make('services')
                    ->relationship('services')
                    ->label('Bảng dịch vụ thêm')
                    ->grid(5)
                    ->live(onBlur: true)
                    ->table([

                        TableColumn::make('Dịch vụ'),

                        TableColumn::make('Giá dịch vụ'),

                        TableColumn::make('% Vat'),

                        TableColumn::make('Số hoá đơn'),

                        TableColumn::make('Ghi chú'),

                    ])
                    ->schema([
                        Select::make('service_id')
                            ->options(Service::query()->pluck('name', 'id'))
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->distinct(),

                        TextInput::make('price')
                            ->label('💵 Giá dịch vụ')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->required()
                            ->minValue(1000),

                        TextInput::make('vat_rate')
                            ->label('% Vat')
                            ->numeric(),
                        TextInput::make('invoice_number')->label('Số hoá đơn'),
                        TextInput::make('note')->label('Ghi chú'),
                    ])
                    ->afterStateUpdated(function ($state, callable $set) {
                        // $state là toàn bộ mảng repeater
                        $total = 0;
                        $totalVat = 0;

                        foreach ($state as $item) {
                            $price = (float) ($item['price'] ?? 0);
                            $vatRate = (float) ($item['vat_rate'] ?? 0);

                            $total += $price;
                            $totalVat += $price * $vatRate / 100;
                        }

                        // Cập nhật field trong form cha (ở billingTripSection)
                        $set('bill.total_amount_service', $total);
                        $set('bill.vat_amount_service', $totalVat);
                    }),

            ]);
    }

    public static function billingTripSection(): Section
    {
        return
        Section::make('Giá cước & thanh toán')
            ->collapsible()
            ->reactive()
            ->relationship('bill')
            ->schema([
                Fieldset::make('3. GIá cước')
                    ->schema([
                        TextInput::make('price')
                            ->label('💰 Giá cước')
                            ->placeholder('Nhập giá cước')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->suffix('đ')
                            ->minValue(1000)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        TextInput::make('vat_rate_price')
                            ->label('% Vat')
                            ->placeholder('Nhập %')->live(onBlur: true)
                            ->numeric(),
                        TextInput::make('truckload_price')
                            ->label('💰 Giá bốc xếp')
                            ->placeholder('Nhập giá bốc xếp')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->minValue(1000)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        TextInput::make('vat_rate_truckload')
                            ->label('% Vat')
                            ->placeholder('Nhập %')->live(onBlur: true)
                            ->numeric()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        TextInput::make('price_back')
                            ->label('💰 Giá quay đầu')
                            ->placeholder('Nhập giá quay đầu')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->minValue(1000)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotals($set, $get);
                            }),
                        TextInput::make('vat_rate_price_back')
                            ->label('% Vat')
                            ->placeholder('Nhập %')
                            ->live(onBlur: true)
                            ->numeric(),
                    ]),
                Fieldset::make('3. Tạm tính')
                    ->schema([
                        TextInput::make('total_amount_service')
                            ->required()
                            ->label('💰 Tổng giá dịch vụ')
                            ->readOnly()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')->numeric()
                            ->suffix('đ')
                            ->extraAttributes([
                                'class' => 'font-bold',
                            ]),
                        TextInput::make('vat_amount_service')
                            ->required()
                            ->label('💰 Tổng VAT dịch vụ')
                            ->readOnly()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')->numeric()
                            ->suffix('đ')
                            ->extraAttributes([
                                'class' => 'font-bold',
                            ]),
                        TextInput::make('total_price')
                            ->required()
                            ->label('💰 Tổng cước')
                            ->readOnly()->reactive()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')->numeric()
                            ->suffix('đ')
                            ->extraAttributes([
                                'class' => 'font-bold',
                            ]),
                        TextInput::make('total_paid')
                            ->required()
                            ->label('💰 Tổng thanh toán')
                            ->readOnly()->reactive()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')->numeric()->suffix('đ')
                            ->extraAttributes([
                                'class' => 'font-bold',
                            ]),
                    ]),
                ToggleButtons::make('invoice_issued')
                    ->label('Xuất hoá đơn')
                    ->default(false) // 👈 mặc định false
                    ->inlineLabel(false)
                    ->inline(true)
                    ->required()
                    ->options([
                        true => 'Có',
                        false => 'Không',
                    ])
                    ->colors([
                        true => 'success',
                        false => 'gray',
                    ])
                    ->icons([
                        true => 'heroicon-o-check-circle',
                        false => 'heroicon-o-x-circle',
                    ])
                    ->reactive(),

            ]);
    }

    protected static function updateTotals(callable $set, callable $get): void
    {
        // Lấy giá trị *chuỗi* thô từ form state (ví dụ: "1,000,000đ") và làm sạch.
        $price = $get('price');
        $truckload = $get('truckload_price');
        $priceBack = $get('price_back');
        $vatPrice = 1 + $get('vat_rate_price') / 100;
        $vat_rate_truckload = 1 + $get('vat_rate_truckload') / 100;
        $vat_rate_price_back = 1 + $get('vat_rate_price_back') / 100;
        // Bỏ dd($get('price')); đi.

        // Giá trị từ Repeater (total_amount_service, vat_amount_service)
        // đã được làm sạch ở Repeater, nên chỉ cần ép kiểu float.
        $totalService = (float) $get('total_amount_service');
        $vatService = (float) $get('vat_amount_service');
        $set('total_price', ($price + $truckload + $priceBack + $totalService));
        $set('total_paid', (($price * $vatPrice) + ($truckload * $vat_rate_truckload) +
        ($priceBack * $vat_rate_price_back) + $totalService + $vatService + $vatService));
    }

    public static function containerSection(): Section
    {
        return Section::make('')
            ->relationship('containerInfo')
            ->schema([
                Fieldset::make('2. Thống tin điểm lên')
                    ->schema([

                    ]),
                DatePicker::make('pick_up_date')
                    ->default(now()->startOfDay())
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->native(false)
                    ->label('📅 Ngày lên hàng, lấy hàng')
                    ->placeholder('Chọn ngày lên hàng')
                    ->required()
                    ->reactive(),

                TimePicker::make('pick_up_time')
                    ->label('⏰ Giờ lên hàng, lấy hàng')
                    ->placeholder('Chọn hoặc nhập giờ')
                    ->format('H:i')
                    ->displayFormat('H:i')
                    ->seconds(false)
                    ->datalist(getTimeData()),

            ]);
    }

    public static function configure(Schema $schema): Schema
    {

        return $schema->components(array_merge(
            self::baseComponents(),
            [self::tripSection(), self::containerSection()] // mặc định nếu bạn muốn
        ));
    }
}
