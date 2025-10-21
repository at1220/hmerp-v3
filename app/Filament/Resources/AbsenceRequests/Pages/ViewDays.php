<?php

namespace App\Filament\Resources\AbsenceRequests\Pages;

use App\Filament\Resources\AbsenceRequests\AbsenceRequestResource;
use App\Filament\Resources\AbsenceRequests\Tables\AbsenceDaysTable; // Import class cấu hình bảng
use App\Filament\Tables\AbsenceDaysTable as TablesAbsenceDaysTable;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ViewDays extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = AbsenceRequestResource::class;

    protected string $view = 'filament.resources.absence-requests.pages.view-days';

    protected static ?string $title = 'Danh sách ngày nghỉ';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function table(Table $table): Table
    {
        // 1. Áp dụng cấu hình Columns, Actions, Filters, Sorts từ class AbsenceDaysTable
        $table = TablesAbsenceDaysTable::configure($table);

        // 2. Chỉ định Query cho bảng (phải ở đây vì cần $this->record)
        return $table
            ->query(
                $this->record->day()->getQuery()->where('status', '!=', 'cancel')
            );

        // Lưu ý: Các phần ->columns(), ->filters(), ->defaultSort()
        // đã được chuyển vào AbsenceDaysTable.php
    }
}
