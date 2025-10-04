<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enum\UserStatus;
use App\Filament\Resources\Users\Schemas\UserForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('customer.is_company')->label('loại khách')
                    ->visible(fn ($livewire): bool => $livewire->activeTab === 'customer'),
                TextColumn::make('staff.outside_truck')->label('xe ngoài')
                    ->visible(fn ($livewire): bool => $livewire->activeTab === 'staff'),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => safeEnumLabel(UserStatus::class, $state)),
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
                // TrashedFilter::make(),
                SelectFilter::make('outside_truck')
                    ->label('Loại xe')
                    ->options([
                        1 => 'Xe ngoài',
                        0 => 'Xe công ty',
                    ])
                    ->query(function ($query, $value) {
                        if (filled($value)) {
                            $query->whereHas('staff', function ($q) use ($value) {
                                $q->where('outside_truck', $value);
                            });
                        }
                    })
                    ->visible(fn ($livewire): bool => $livewire->activeTab === 'staff'),

            ])

            ->recordActions([
                EditAction::make('editStaff')
                   // ->label('Cập nhật nhân viên')
                    ->modalHeading('Cập nhật nhân viên')
                    ->schema(array_merge(
                        UserForm::baseComponents(),
                        [UserForm::staffSection()]
                    ))
                    ->visible(fn ($record) => $record->role == 'admin'),
                EditAction::make('editCustomer')
                    // ->label('Thêm khách hàng')
                    ->modalHeading('Cập nhật khách hàng')
                    ->schema(array_merge(
                        UserForm::baseComponents(),
                        [UserForm::customerSection()]
                    ))
                    ->visible(fn ($record) => $record->role != 'admin'),
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
