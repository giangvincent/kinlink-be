<?php

namespace App\Filament\Resources;

use App\Enums\InvitationRole;
use App\Filament\Resources\InvitationResource\Pages;
use App\Models\Invitation;
use App\Services\InvitationService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('family_id')
                ->relationship('family', 'name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
            Forms\Components\Select::make('role')
                ->options(collect(InvitationRole::cases())->mapWithKeys(fn ($role) => [$role->value => ucfirst($role->value)])->all())
                ->required(),
            Forms\Components\DateTimePicker::make('expires_at')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('family.name')->label('Family')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('accepted_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('accepted_at')
                    ->label('Accepted')
                    ->nullable()
                    ->trueLabel('Accepted')
                    ->falseLabel('Pending'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (Invitation $record) {
                        app(InvitationService::class)->createInvitation(
                            $record->family,
                            $record->email,
                            $record->role,
                            auth()->user()
                        );
                    }),
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Invitation $record) => $record->delete())
                    ->hidden(fn (Invitation $record) => $record->accepted_at !== null),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }
}
