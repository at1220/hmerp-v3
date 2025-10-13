<?php

namespace App\Filament\Resources\AbsenceRequests\Schemas;

use App\Models\AbsenceRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class AbsenceRequestForm
{
    public static function oneDaySection(): Section
    {
        return Section::make('Đơn nghỉ 1 ngày')
            ->schema([
                DatePicker::make('from_date')
                    ->label('📅 Ngày nghỉ')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->native(false)
                    ->required(),
                TextInput::make('reason')
                    ->label('📝 Lý do nghỉ')
                    ->required(),
                Textarea::make('description')
                    ->label('📝 Ghi chú'),
                Select::make('part_of_day')
                    ->label('📝 Thời gian nghỉ')
                    ->options([
                        'day' => 'Nghỉ cả ngày',
                        'morning' => 'Nghỉ buổi sáng',
                        'afternoon' => 'Nghỉ buổi chiều',
                    ])
                    ->required()
                    ->hidden(fn (string $operation): bool => $operation == 'create_one_day')
                    ->default('day'),
                Select::make('part_of_day')
                    ->label('📝 Thời gian nghỉ')
                    ->options([
                        'day' => 'Nghỉ cả ngày',
                        'morning' => 'Nghỉ buổi sáng',
                        'afternoon' => 'Nghỉ buổi chiều',
                    ])
                    ->required()

                    ->hidden(fn (string $operation): bool => $operation == 'edit_one_day'),
            ]);
    }

    public static function multiDaysSection(): Section
    {
        return Section::make('Đơn nghỉ nhiều ngày')
            ->schema([
                DatePicker::make('from_date')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->format('Y-m-d')
                    ->label('📅 Từ ngày')
                    ->required(),

                DatePicker::make('to_date')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->format('Y-m-d')
                    ->label('📅 Đến ngày')
                    ->required(),
                Textarea::make('reason')
                    ->label('📝 Lý do nghỉ')
                    ->required(),
                Textarea::make('description')
                    ->label('📝 Ghi chú'),
            ]);
    }

    public static function absenceDaySection(): Section
    {
        return Section::make('Danh sách ngày nghỉ')
            ->label('')
            ->footer([
                Action::make('saveDays')
                    ->label('💾 Lưu danh sách ngày nghỉ')
                    ->button()
                    ->color('success')
                    ->visible(fn ($record) => $record?->status == 'pending')
                    ->action(function (callable $get) {
                        $days = $get('day') ?? [];

                        if (empty($days)) {
                            Notification::make()
                                ->title('Không có dữ liệu để lưu!')
                                ->warning()
                                ->send();

                            return;
                        }

                        // Lấy absence_id từ phần tử đầu tiên
                        $absenceId = $days[array_key_first($days)]['absence_id'] ?? null;

                        if (! $absenceId) {
                            Notification::make()
                                ->title('Thiếu absence_id, không thể lưu!')
                                ->danger()
                                ->send();

                            return;
                        }

                        $absence = AbsenceRequest::find($absenceId);

                        if (! $absence) {
                            Notification::make()
                                ->title("Không tìm thấy đơn nghỉ #{$absenceId}!")
                                ->danger()
                                ->send();

                            return;
                        }

                        DB::transaction(function () use ($absence, $days) {
                            // 1️⃣ Xóa cứng toàn bộ bản ghi cũ (bỏ qua soft delete)
                            $absence->day()->forceDelete();

                            // 2️⃣ Tạo lại danh sách mới
                            foreach ($days as $dayData) {
                                $data = collect($dayData)
                                    ->only(['date', 'part_of_day', 'status', 'leave_type'])
                                    ->toArray();

                                $absence->day()->create($data);
                            }
                        });

                        Notification::make()
                            ->title('Cập nhật danh sách ngày nghỉ thành công!')
                            ->success()
                            ->send();
                    }),
            ])
            ->schema([
                Repeater::make('day')
                    ->label('Chi tiết ngày nghỉ')
                    ->extraAttributes(['class' => 'hidden'])
                    ->addable(false)
                    ->relationship() // 👈 hasMany(OrderService)
                    ->grid(5)
                    ->table([
                        TableColumn::make('Ngày'),
                        TableColumn::make('Buổi'),
                        TableColumn::make('Trạng thái'),

                    ])
                    ->schema([
                        DatePicker::make('date')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->format('Y-m-d')
                            ->label('Ngày')
                            ->readOnly(),

                        Select::make('part_of_day')
                            ->label('Buổi')
                            ->options([
                                'day' => 'Nghỉ cả ngày',
                                'morning' => 'Nghỉ buổi sáng',
                                'afternoon' => 'Nghỉ buổi chiều',
                            ])
                            ->required(),
                        TextInput::make('status')
                            ->label('Trạng thái')
                            ->readOnly(),
                    ]),

            ]);
    }

    public static function configure(Schema $schema, string $type = 'one_day'): Schema
    {

        return match ($type) {
            'one_day' => $schema->components([
                self::oneDaySection(),
                self::absenceDaySection(),
            ]),

            'multi_day' => $schema->components([
                self::multiDaysSection(),
                self::absenceDaySection(),
            ]),
            'create_multi_day' => $schema->components([
                self::multiDaysSection(),

            ]),
            'create_one_day' => $schema->components([
                self::oneDaySection(),

            ]),
            default => $schema->components([]),
        };
    }
}
