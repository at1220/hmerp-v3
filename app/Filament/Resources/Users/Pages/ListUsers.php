<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        $currentUser = Auth::user()->role;

        return [

            'staff' => Tab::make('Nhân viên')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
            // ->visible(fn () => $currentUser != 'admin')
            ,
            'customer' => Tab::make('Khách hàng')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'customer')),

        ];
    }

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
