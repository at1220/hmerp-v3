<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enum\Order\Type;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class OrdersTable
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
                TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->formatStateUsing(function ($state, $record, $livewire) {
                        return highlightSearch($state, $livewire->getTableSearch());
                    })
                    ->html() // bắt buộc: cho phép render HTML
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Loại đơn')
                    ->searchable(),
                TextColumn::make('formattedPickUpDate')
                    ->label('Ngày giờ lên hàng'),
            ])
            ->filters([
                Filter::make('pick_up_date_range')
                    ->schema([
                        DatePicker::make('from')->label('Từ ngày')->native(false)
                            ->displayFormat('d/m/Y')->format('Y-m-d'),
                        DatePicker::make('until')->label('Đến ngày')->native(false)
                            ->displayFormat('d/m/Y')->format('Y-m-d'),
                    ])
                    ->query(function ($query, array $data) {

                        return $query->where(function ($q) use ($data) {
                            $from = $data['from'];
                            $until = $data['until'];

                            $q->where(function ($q) use ($from, $until) {
                                $q->whereHas('tripInfo', function ($sub) use ($from, $until) {
                                    $sub->when($from, fn ($qq) => $qq->where('pick_up_date', '>=', $from))
                                        ->when($until, fn ($qq) => $qq->where('pick_up_date', '<=', $until));
                                })
                                    ->orWhereHas('containerInfo', function ($sub) use ($from, $until) {
                                        $sub->when($from, fn ($qq) => $qq->where('pick_up_date', '>=', $from))
                                            ->when($until, fn ($qq) => $qq->where('pick_up_date', '<=', $until));
                                    });
                            });
                        });
                    }),
            ])
            ->recordActions([

                ActionGroup::make([
                    //
                    ViewAction::make('viewTrip')
                        ->label('xem Đơn hàng chuyến')
                        ->modalHeading('Chi tiết đơn')
                        ->schema(array_merge(
                            OrderForm::baseComponents(),
                            [OrderForm::tripSection()]
                        ))
                        ->visible(fn ($record) => $record->type == Type::Trip)
                        ->mutateRecordDataUsing(function (array $data): array {

                            return $data;
                        }),
                    EditAction::make('editTrip')
                        ->label('Đơn hàng chuyến')
                        ->modalDescription('Đây là sửa')
                        ->modalHeading('Tạo đơn hàng chuyến')
                        ->schema(array_merge(
                            OrderForm::baseComponents(),
                            [OrderForm::tripSection(), OrderForm::billingTripSection()]// OrderForm::serviceSection(),
                        ))
                        ->visible(fn ($record) => $record->type == Type::Trip),

                    EditAction::make('editContainer')
                        ->label('Đơn hàng container')
                        ->modalHeading('Tạo đơn hàng container')
                        ->schema(array_merge(
                            OrderForm::baseComponents(),
                            [OrderForm::containerSection()]
                        ))
                        ->visible(fn ($record) => $record->type == Type::Container),

                ])
                    ->icon('heroicon-m-cog-6-tooth')
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
