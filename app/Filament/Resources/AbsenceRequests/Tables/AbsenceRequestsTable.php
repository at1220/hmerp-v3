<?php

namespace App\Filament\Resources\AbsenceRequests\Tables;

use App\Filament\Resources\AbsenceRequests\Schemas\AbsenceRequestForm;
use App\Models\AbsenceApproval;
use App\Models\AbsenceDay;
use App\Models\AbsenceRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AbsenceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(function ($state, $record, $livewire) {
                        return highlightSearch($state, $livewire->getTableSearch());
                    })
                    ->html() // bắt buộc: cho phép render HTML
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(function ($state, $record, $livewire) {
                        return highlightSearch($state, $livewire->getTableSearch());
                    })
                    ->html() // bắt buộc: cho phép render HTML
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('NV nghỉ')
                    ->formatStateUsing(function ($state, $record, $livewire) {
                        return highlightSearch($state, $livewire->getTableSearch());
                    })
                    ->html() // bắt buộc: cho phép render HTML
                    ->searchable(),
                TextColumn::make('formattedDate')
                    ->label('Ngày nghỉ'),
                TextColumn::make('total_day')
                    ->label('Ngày nghỉ'),
                TextColumn::make('reason')
                    ->label('Lí do'),
                TextColumn::make('description')
                    ->label('Ghi chú'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([

                ActionGroup::make([
                    ViewAction::make('view')
                        ->label('Xem chi tiết')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Chi tiết đơn nghỉ')
                        ->modalWidth('2xl')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('user.name')->label('Nhân viên')
                                        ->inlineLabel()
                                        ->color('primary'),
                                    TextEntry::make('status')->label('Trạng thái')
                                        ->inlineLabel()
                                        ->color('primary'),

                                    TextEntry::make('from_date')->label('Từ ngày')->date('d/m/Y')
                                        ->inlineLabel()
                                        ->color('primary'),
                                    TextEntry::make('to_date')->label('Đến ngày')->date('d/m/Y')
                                        ->inlineLabel()
                                        ->color('primary'),

                                    TextEntry::make('total_day')->label('Số ngày')
                                        ->inlineLabel()
                                        ->color('primary'),
                                    TextEntry::make('reason')->label('Lý do')
                                        ->inlineLabel()
                                        ->color('primary'),
                                    TextEntry::make('description')
                                        ->label('Ghi chú')
                                        ->inlineLabel()
                                        ->color('primary'),
                                ]),
                            RepeatableEntry::make('day') // relation `day()`
                                ->label('Ngày nghỉ')
                                ->table([
                                    TableColumn::make('Ngày'),
                                    TableColumn::make('Buổi'),
                                    TableColumn::make('Trạng thái'),
                                ])
                                ->schema([
                                    TextEntry::make('date')->date('d/m/Y'),
                                    TextEntry::make('part_of_day'),
                                    TextEntry::make('status')->color('primary'),
                                ]),

                        ]),
                    EditAction::make('edit_one_day')
                        ->modalHeading('Cập nhật')
                        ->visible(fn ($record) => $record->from_date == $record->to_date && $record->status == 'pending')
                        ->schema(fn () => AbsenceRequestForm::configure(app(Schema::class),
                            'one_day')->getComponents())
                        ->beforeFormFilled(function (AbsenceRequest $record) {
                            $record->part_of_day = $record->day->first()?->part_of_day;
                        })
                        ->mutateDataUsing(function (array $data): array {
                            $data['from_date'] = $data['from_date'];
                            $data['to_date'] = $data['from_date'];
                            $data['user_id'] = Auth::id();
                            $data['created_by'] = Auth::id();
                            $data['status'] = 'pending';
                            $data['total_day'] = $data['part_of_day'] == 'day' ? 1 : 0.5;

                            return $data;
                        })

                        ->using(function (array $data, $record): Model {
                            unset($data['date']);
                            unset($data['part_of_day']);
                            $record->update($data);

                            return $record;
                        })
                        ->after(function ($record, array $data) {
                            AbsenceDay::where('absence_id', $record->id)->delete();
                            AbsenceDay::create([
                                'status' => 'pending',
                                'absence_id' => $record->id,
                                'date' => $record->from_date,
                                'part_of_day' => $data['part_of_day'],
                                'leave_type' => 'none',
                            ]);
                            AbsenceApproval::create([
                                'approval_id' => null,
                                'absence_id' => $record->id,
                                'level' => 1,
                                'status' => 'pending',
                                'note' => null,
                            ]);
                        }),

                    EditAction::make('edit_multi_day')
                        ->label('Cập nhật ngày nghỉ')
                        ->modalHeading('Cập nhật')
                        ->visible(fn ($record) => ($record->from_date != $record->to_date && ($record->status == 'pending' || $record->status == 'waiting')))
                        ->schema(fn () => AbsenceRequestForm::configure(app(Schema::class), 'multi_day')->getComponents())
                        ->mutateDataUsing(function (array $data): array {
                            $from = Carbon::parse($data['from_date']); // từ form
                            $to = Carbon::parse($data['to_date']);   // từ form

                            // tạo period
                            $period = CarbonPeriod::create($from, $to);

                            // lọc ra ngày không phải Chủ nhật
                            $totalDays = collect($period)
                                ->filter(fn ($date) => $date->dayOfWeek !== Carbon::SUNDAY)
                                ->count();
                            $data['user_id'] = Auth::id();
                            $data['created_by'] = Auth::id();
                            $data['status'] = 'pending';
                            $data['total_day'] = $totalDays;

                            return $data;
                        })
                        ->using(function (array $data, $record): Model {
                            $record->update($data);

                            return $record;
                        })
                        ->after(function ($record, array $data) {
                            $from = Carbon::parse($record->from_date);
                            $to = Carbon::parse($record->to_date);

                            $period = CarbonPeriod::create($from, $to);

                            AbsenceDay::where('absence_id', $record->id)->forceDelete();
                            $days = collect();
                            foreach ($period as $date) {
                                // bỏ qua Chủ nhật
                                if ($date->dayOfWeek === Carbon::SUNDAY) {
                                    continue;
                                }

                                $day = AbsenceDay::create([
                                    'status' => 'pending',
                                    'absence_id' => $record->id,
                                    'date' => $date->format('Y-m-d'),
                                    'part_of_day' => 'day', // day hoặc half
                                    'leave_type' => 'none',
                                ]);
                                $days->push($day);
                            }
                            $dayCount = $days->count();
                            $levels = $dayCount > 5 ? [1, 2, 3] : [1, 2];

                            foreach ($levels as $level) {
                                AbsenceApproval::create([
                                    'approval_id' => null,
                                    'absence_id' => $record->id,
                                    'level' => $level,
                                    'status' => 'pending',
                                    'note' => null,
                                ]);
                            }
                        }),
                ])->icon('heroicon-m-cog-6-tooth')
                    ->label(''),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
