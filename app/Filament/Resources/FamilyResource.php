<?php

namespace App\Filament\Resources;

use App\Enums\BillingPlan;
use App\Filament\Resources\FamilyResource\Pages;
use App\Filament\Resources\FamilyResource\Widgets\FamilyOverviewStats;
use App\Models\Family;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction as FilamentEditAction;
use Filament\Actions\DeleteBulkAction;

class FamilyResource extends Resource
{
    protected static ?string $model = Family::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Slug' => $record->slug,
            'Locale' => $record->locale,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\Select::make('billing_plan')
                ->options(collect(BillingPlan::cases())->mapWithKeys(fn ($plan) => [$plan->value => ucfirst($plan->value)])->all())
                ->default(BillingPlan::FREE->value),
            Forms\Components\TextInput::make('locale')
                ->label('Locale')
                ->default('en')
                ->maxLength(12),
            Forms\Components\KeyValue::make('settings')
                ->keyLabel('Key')
                ->valueLabel('Value')
                ->default([])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('locale'),
                Tables\Columns\TextColumn::make('billing_plan')
                    ->badge(),
                Tables\Columns\TextColumn::make('members_count')
                    ->counts('members')
                    ->label('Members'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('billing_plan')
                    ->options(collect(BillingPlan::cases())->mapWithKeys(fn ($plan) => [$plan->value => ucfirst($plan->value)])->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                FilamentEditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            FamilyOverviewStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilies::route('/'),
            'create' => Pages\CreateFamily::route('/create'),
            'edit' => Pages\EditFamily::route('/{record}/edit'),
            'view' => Pages\ViewFamily::route('/{record}'),
        ];
    }
}
