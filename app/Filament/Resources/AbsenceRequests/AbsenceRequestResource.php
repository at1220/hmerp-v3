<?php

namespace App\Filament\Resources\AbsenceRequests;

use App\Filament\Resources\AbsenceRequests\Pages\CreateAbsenceRequest;
use App\Filament\Resources\AbsenceRequests\Pages\EditAbsenceRequest;
use App\Filament\Resources\AbsenceRequests\Pages\ListAbsenceRequests;
use App\Filament\Resources\AbsenceRequests\Schemas\AbsenceRequestForm;
use App\Filament\Resources\AbsenceRequests\Tables\AbsenceRequestsTable;
use App\Models\AbsenceRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsenceRequestResource extends Resource
{
    protected static ?string $model = AbsenceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'user_id';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('filament.resources.absence.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.absence.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return AbsenceRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsenceRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsenceRequests::route('/'),
            // 'create' => CreateAbsenceRequest::route('/create'),
            // 'edit' => EditAbsenceRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
