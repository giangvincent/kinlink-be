<?php

namespace App\Filament\Resources;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Enums\RelationshipType;
use App\Filament\Resources\PersonResource\Pages;
use App\Models\Person;
use App\Services\PersonMergeService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction as FilamentEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function getGloballySearchableAttributes(): array
    {
        return ['display_name', 'given_name', 'surname'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('display_name')->required(),
            Forms\Components\TextInput::make('given_name')->required(),
            Forms\Components\TextInput::make('middle_name'),
            Forms\Components\TextInput::make('surname')->required(),
            Forms\Components\Select::make('gender')
                ->options(collect(PersonGender::cases())->mapWithKeys(fn ($gender) => [$gender->value => ucfirst(str_replace('_', ' ', $gender->value))])->all()),
            Forms\Components\DatePicker::make('birth_date'),
            Forms\Components\DatePicker::make('death_date'),
            Forms\Components\Select::make('visibility')
                ->options(collect(PersonVisibility::cases())->mapWithKeys(fn ($visibility) => [$visibility->value => ucfirst($visibility->value)])->all()),
            Forms\Components\Textarea::make('meta')
                ->rows(4)
                ->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')->searchable()->sortable(),
                TextColumn::make('surname')->searchable(),
                TextColumn::make('gender')->badge(),
                TextColumn::make('visibility')->badge(),
                TextColumn::make('family.name')->label('Family'),
            ])
            ->filters([
                SelectFilter::make('gender')->options(collect(PersonGender::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst($case->value)])->all()),
                SelectFilter::make('visibility')->options(collect(PersonVisibility::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst($case->value)])->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                FilamentEditAction::make(),
                Action::make('mergeDuplicates')
                    ->label('Merge Duplicate')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        Forms\Components\Select::make('target_person_id')
                            ->label('Target Person')
                            ->required()
                            ->searchable()
                            ->options(fn (Person $record) => Person::query()
                                ->where('family_id', $record->family_id)
                                ->whereKeyNot($record->getKey())
                                ->pluck('display_name', 'id')),
                    ])
                    ->requiresConfirmation()
                    ->action(function (array $data, Person $record) {
                        app(PersonMergeService::class)->merge($record, Person::findOrFail($data['target_person_id']));
                    }),
                Action::make('reparentChild')
                    ->label('Reparent Child')
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->schema([
                        Forms\Components\Select::make('child_id')
                            ->label('Child')
                            ->required()
                            ->searchable()
                            ->options(fn (Person $record) => $record->primaryRelationships()
                                ->where('type', RelationshipType::PARENT->value)
                                ->with('personB')
                                ->get()
                                ->pluck('personB.display_name', 'personB.id')),
                        Forms\Components\Select::make('new_parent_id')
                            ->label('New Parent')
                            ->required()
                            ->searchable()
                            ->options(fn (Person $record) => Person::query()
                                ->where('family_id', $record->family_id)
                                ->whereKeyNot($record->getKey())
                                ->pluck('display_name', 'id')),
                    ])
                    ->hidden(fn (Person $record) => $record->primaryRelationships()->where('type', RelationshipType::PARENT->value)->doesntExist())
                    ->action(function (array $data, Person $record) {
                        app(PersonMergeService::class)->reparentChild(
                            childId: $data['child_id'],
                            oldParent: $record,
                            newParentId: (int) $data['new_parent_id']
                        );
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
            'view' => Pages\ViewPerson::route('/{record}'),
        ];
    }
}
