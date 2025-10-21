<?php

namespace App\Filament\Tables;

use App\Models\AbsenceDay;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsenceDaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => AbsenceDay::query())
            ->columns([
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('absence_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('part_of_day')
                    ->searchable(),
                TextColumn::make('leave_type')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('cancel')
                        ->label('Huỷ đơn')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Bạn có chắc muốn huỷ đơn này không?')
                        ->modalDescription('Thao tác này sẽ cập nhật trạng thái của đơn thành "Huỷ".')
                        ->modalSubmitActionLabel('Đồng ý')
                        ->action(function ($record) {
                            $record->update(['status' => 'cancel']);

                            Notification::make()
                                ->title('Đơn đã được huỷ thành công.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn ($record) => $record->status !== 'cancel'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
