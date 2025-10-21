<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('collection_name')->label('Collection')->required(),
            TextInput::make('name')->required(),
            TextInput::make('file_name')->required(),
            TextInput::make('mime_type'),
            TextInput::make('custom_properties')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')->label('UUID')->copyable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('collection_name')->label('Collection'),
                TextColumn::make('model_type')->label('Model'),
                TextColumn::make('size')->label('Size (KB)')->formatStateUsing(fn ($state) => number_format($state / 1024, 2)),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
                ->recordActions([
                    ViewAction::make(),
                    DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
