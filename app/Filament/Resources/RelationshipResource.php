<?php

namespace App\Filament\Resources;

use App\Enums\RelationshipType;
use App\Filament\Resources\RelationshipResource\Pages;
use App\Models\Relationship;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;

class RelationshipResource extends Resource
{
    protected static ?string $model = Relationship::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('person_id_a')
                ->relationship('personA', 'display_name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('person_id_b')
                ->relationship('personB', 'display_name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('type')
                ->options(collect(RelationshipType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->value])->all())
                ->required(),
            Forms\Components\TextInput::make('certainty')->numeric()->default(100),
            Forms\Components\TextInput::make('source'),
            Forms\Components\Textarea::make('notes')->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('family.name')->label('Family')->sortable(),
                Tables\Columns\TextColumn::make('personA.display_name')->label('Person A')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('personB.display_name')->label('Person B')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('certainty')->suffix('%'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(collect(RelationshipType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->value])->all()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('setType')
                    ->label('Set type')
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('New Type')
                            ->required()
                            ->options(collect(RelationshipType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->value])->all()),
                    ])
                    ->action(fn (array $data, $records) => $records->each->update(['type' => $data['type']]))
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('normalizeCertainty')
                    ->label('Normalize certainty')
                    ->form([
                        Forms\Components\TextInput::make('certainty')
                            ->numeric()
                            ->default(90)
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    ])
                    ->action(fn (array $data, $records) => $records->each->update(['certainty' => $data['certainty']]))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRelationships::route('/'),
            'create' => Pages\CreateRelationship::route('/create'),
            'edit' => Pages\EditRelationship::route('/{record}/edit'),
        ];
    }
}
