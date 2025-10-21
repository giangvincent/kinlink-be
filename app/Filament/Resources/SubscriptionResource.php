<?php

namespace App\Filament\Resources;

use App\Enums\BillingPlan;
use App\Enums\SubscriptionProvider;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use App\Services\BillingService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('family_id')
                ->relationship('family', 'name')
                ->required(),
            Forms\Components\Select::make('provider')
                ->options(collect(SubscriptionProvider::cases())->mapWithKeys(fn ($provider) => [$provider->value => ucfirst($provider->value)])->all())
                ->required(),
            Forms\Components\Select::make('status')
                ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => ucfirst(str_replace('_', ' ', $status->value))])->all())
                ->required(),
            Forms\Components\DateTimePicker::make('current_period_end'),
            Forms\Components\TextInput::make('seats')->numeric()->default(0),
            Forms\Components\TextInput::make('storage_quota_mb')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('family.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('provider')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('seats'),
                Tables\Columns\TextColumn::make('storage_quota_mb')->label('Storage (MB)'),
                Tables\Columns\TextColumn::make('current_period_end')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('changePlan')
                    ->label('Change Plan')
                    ->form([
                        Forms\Components\Select::make('plan')
                            ->options(collect(BillingPlan::cases())->mapWithKeys(fn ($plan) => [$plan->value => ucfirst($plan->value)])->all())
                            ->required(),
                        Forms\Components\TextInput::make('seats')->numeric()->default(fn (Subscription $record) => $record->seats),
                    ])
                    ->action(function (array $data, Subscription $record) {
                        $family = $record->family;
                        $family->billing_plan = BillingPlan::from($data['plan']);
                        $family->save();

                        $record->update([
                            'seats' => (int) $data['seats'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
