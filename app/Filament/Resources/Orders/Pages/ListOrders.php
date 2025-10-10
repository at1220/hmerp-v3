<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enum\Order\Type;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createTrip')
                ->label('Đơn hàng chuyến')
                ->modalHeading('Tạo đơn hàng chuyến')
                ->schema(array_merge(
                    OrderForm::baseComponents(),
                    [OrderForm::tripSection(), OrderForm::billingTripSection()]// OrderForm::serviceSection(),
                ))
                ->mutateDataUsing(function (array $data): array {
                    // password mặc định
                    $code = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                    $data['order_code'] = $code;
                    $data['type'] = Type::Trip;
                    $data['status'] = 'new';
                    $data['status_payment'] = 'pendding';
                    $data['created_by'] = Auth::user()->id;

                    return $data;
                }),
            CreateAction::make('createContainer')
                ->label('Đơn hàng container')
                ->modalHeading('Tạo đơn hàng container')
                ->schema(array_merge(
                    OrderForm::baseComponents(),
                    [OrderForm::containerSection()]
                ))

                ->mutateDataUsing(function (array $data): array {
                    $code = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                    $data['order_code'] = $code;
                    $data['type'] = Type::Trip;
                    $data['status'] = 'new';
                    $data['status_payment'] = 'pendding';
                    $data['created_by'] = Auth::user()->id;
                    dd($data);

                    return $data;
                }),
        ];
    }
}
