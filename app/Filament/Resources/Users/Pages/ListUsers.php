<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createStaff')
                ->label('Thêm nhân viên')
                ->modalHeading('Tạo nhân viên')
                ->schema(array_merge(
                    UserForm::baseComponents(),
                    [UserForm::staffSection()]
                ))
                ->mutateDataUsing(function (array $data): array {
                    // password mặc định
                    $data['password'] = '123';

                    return $data;
                }),
            CreateAction::make('createCustomer')
                ->label('Thêm khách hàng')
                ->modalHeading('Tạo khách hàng')
                ->schema(array_merge(
                    UserForm::baseComponents(),
                    [UserForm::customerSection()]
                ))

                ->mutateDataUsing(function (array $data): array {
                    $data['password'] = '123';

                    return $data;
                }),
        ];
    }
}
