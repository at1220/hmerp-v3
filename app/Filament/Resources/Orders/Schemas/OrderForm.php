<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enum\Order\HandlingMethod;
use App\Enum\Order\TypeArise;
use App\Enum\Order\TypeVehicle;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
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

    public static function billingTripSection(): Section
    {
        return
        Section::make('💰 Thông tin thanh toán & Dịch vụ')
            ->schema([

                // ====== NHÓM BILL (hasOne) ======
                Fieldset::make('Giá cước & VAT')
                    ->relationship('bill') // 👈 hasOne(OrderBilling)
                    ->reactive() // 🔥 ép cả nhóm `bill` trở thành reactive
                    ->afterStateUpdated(fn (callable $set, callable $get) => updateTotals($set, $get))
                    ->schema([
                        TextInput::make('price')
                            ->label('Giá cước')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->required()
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('vat_rate_price')
                            ->label('% VAT cước')
                            ->numeric()
                            ->live(onBlur: true)
                        // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                        ,

                        TextInput::make('truckload_price')
                            ->label('Giá bốc xếp')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('vat_rate_truckload')
                            ->label('% VAT bốc xếp')
                            ->numeric()
                            ->live(onBlur: true)
                        // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                        ,

                        TextInput::make('price_back')
                            ->label('Giá quay đầu')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->live(onBlur: true)
                           // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('vat_rate_price_back')
                            ->label('% VAT quay đầu')
                            ->numeric()
                            ->live(onBlur: true)
                        // ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                        ,
                    ]),

                // ====== NHÓM SERVICES (hasMany) ======
                Repeater::make('services')
                    ->label('Dịch vụ phát sinh')
                    ->relationship('services') // 👈 hasMany(OrderService)
                    ->grid(5)
                    ->table([
                        TableColumn::make('Dịch vụ'),
                        TableColumn::make('Giá dịch vụ'),
                        TableColumn::make('% Vat'),
                        TableColumn::make('Số hoá đơn'),
                        TableColumn::make('Ghi chú'),

                    ])
                    ->schema([
                        Select::make('service_id')
                            ->label('Tên dịch vụ')
                            ->options(\App\Models\Service::pluck('name', 'id'))
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->required(),

                        TextInput::make('price')
                            ->label('Giá dịch vụ')
                            ->mask(RawJs::make('$money($input)'))
                            ->suffix('đ')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get))
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('vat_rate')
                            ->label('% VAT')
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get)),
                        TextInput::make('invoice_number')
                            ->label('Số hoá đơn'),
                        TextInput::make('note')
                            ->label('Ghi chú'),
                    ])
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        $toNumber = fn ($v) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $v);
                        $data['price'] = $toNumber($data['price'] ?? 0);
                        $data['vat_rate'] = (float) ($data['vat_rate'] ?? 0);

                        return $data;
                    })
                    ->afterStateUpdated(fn ($set, $get) => updateTotals($set, $get)),

                // ====== NHÓM TỔNG ======
                Fieldset::make('Tạm tính')
                    ->relationship('bill')
                    ->schema([
                        TextInput::make('total_amount_service')
                            ->mask(RawJs::make('$money($input)'))
                            ->label('Tổng dịch vụ')
                            ->readOnly()
                            ->suffix('đ')
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('vat_amount_service')
                            ->mask(RawJs::make('$money($input)'))
                            ->label('Tổng VAT dịch vụ')
                            ->readOnly()
                            ->suffix('đ')
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('total_price')
                            ->mask(RawJs::make('$money($input)'))
                            ->label('Tổng cước')
                            ->readOnly()
                            ->suffix('đ')
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),

                        TextInput::make('total_paid')
                            ->mask(RawJs::make('$money($input)'))
                            ->label('Tổng thanh toán')
                            ->readOnly()
                            ->suffix('đ')
                            ->extraAttributes(['class' => 'font-bold'])
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace([',', '.', 'đ', ' '], '', (string) $state)),
                    ]),
            ]);

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

    public static function editPageSection(): array
    {
        return [
            Wizard::make([
                Step::make('Thông tin nhân viên')
                    ->columns(2)
                    ->schema([
                        // Các trường này sẽ được bao gồm trong mảng $data của Wizard
                        Fieldset::make('2. Thống tin điểm lên')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Trạng thái')->color('primary'), // Giả sử chỉ đọc ở đây
                                TextEntry::make('staff.name')
                                    ->label('Nhân viên chốt đơn')->color('primary'),
                            ]),
                        Fieldset::make('2. Thống tin điểm lên')
                            ->schema([
                                TextInput::make('status')
                                    ->label('Trạng thái')
                                    ->default('pending')
                                    ->readOnly(), // Giả sử chỉ đọc ở đây
                                Select::make('staff_id')
                                    ->label('Nhân viên chốt đơn')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(Auth::user()->id)
                                    ->options(User::pluck('name', 'id')),
                            ]),

                    ]),

                Step::make('ABC')
                    ->columns(2)
                    ->schema([
                        Fieldset::make('2. Thống tin điểm lên')
                            ->columns(1)
                            ->schema([
                                TextEntry::make('text')
                                    ->label('Trạng thái')->color('primary')->inlineLabel()->default('Đây là đơn thử nghiệm nội bộ, không cần duyệt.'), // Giả sử chỉ đọc ở đây
                                TextEntry::make('staff.name')
                                    ->label('Nhân viên chốt đơn')->color('primary')->inlineLabel(),
                            ]),
                        Fieldset::make('2. Thống tin điểm lên')
                            ->columns(1)
                            ->schema([
                                TextInput::make('status')
                                    ->label('Trạng thái')->inlineLabel()
                                    ->default('pending'), // Giả sử chỉ đọc ở đây
                                Select::make('staff_id')
                                    ->label('Nhân viên ')
                                    ->searchable()->inlineLabel()
                                    ->required()
                                    ->preload()
                                    ->default(Auth::user()->id)
                                    ->options(User::pluck('name', 'id')),
                                Action::make('save_step_2')
                                    ->label('💾 Lưu')
                                    ->color('success')
                                    ->action(function ($record, $state) {
                                        // $data bây giờ là toàn bộ state của Wizard

                                        $stepData = collect($state)->only(['status', 'staff_id'])->toArray();
                                        // 🧩 Cập nhật vào record
                                        $record->update($stepData);
                                        $recipient = $record->staff;
                                        Notification::make()
                                            ->title('Đã lưu thông tin ngày nghỉ thành công!')
                                            ->body('Dữ liệu (Từ ngày, Đến ngày) đã được cập nhật.')
                                            ->success()
                                            ->sendToDatabase($recipient);
                                    }),
                            ]),

                        // Nút lưu riêng step

                    ]),

                Step::make('Chi tiết nghỉ phép')
                    ->columns(2)
                    ->schema([
                        Fieldset::make('2. Thống tin điểm lên')
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Trạng thái')->color('primary'), // Giả sử chỉ đọc ở đây
                                TextEntry::make('staff.name')
                                    ->label('Nhân viên chốt đơn')->color('primary'),
                            ]),
                        Fieldset::make('2. Thống tin điểm lên')
                            ->schema([
                                TextInput::make('status')
                                    ->label('Trạng thái')
                                    ->default('pending')
                                    ->readOnly(), // Giả sử chỉ đọc ở đây
                                Select::make('staff_id')
                                    ->label('Nhân viên chốt đơn')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(Auth::user()->id)
                                    ->options(User::pluck('name', 'id')),
                            ]),
                    ]),
            ])->skippable()->columnSpanFull()->persistStepInQueryString(),

        ];
    }

    public static bool $isEditPage = false;

    public static function configure(Schema $schema): Schema
    {

        if (self::$isEditPage) {
            return $schema->components(self::editPageSection()); // chỉ baseComponents
        }

        return $schema->components(array_merge(
            self::baseComponents(),
            [self::tripSection(), self::containerSection()]
        ));
    }
}
