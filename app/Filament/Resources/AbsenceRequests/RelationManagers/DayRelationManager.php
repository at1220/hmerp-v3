<?php

namespace App\Filament\Resources\AbsenceRequests\RelationManagers;

use App\Filament\Resources\AbsenceRequests\AbsenceRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DayRelationManager extends RelationManager
{
    protected static string $relationship = 'day';

    protected static ?string $relatedResource = AbsenceRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('part_of_day'),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
