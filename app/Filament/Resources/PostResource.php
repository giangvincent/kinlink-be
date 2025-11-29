<?php

namespace App\Filament\Resources;

use App\Enums\PostVisibility;
use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Services\CompressImageService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction as FilamentEditAction;
use Filament\Actions\DeleteBulkAction;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'KinLink';

    public static function getGloballySearchableAttributes(): array
    {
        return ['body', 'excerpt'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Media')
                ->schema([
                    FileUpload::make('cover_image_path')
                        ->label('Cover Image')
                        ->disk('r2')
                        ->directory('posts/covers')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->maxSize(5120)
                        ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                            return CompressImageService::compress('posts/covers/', $file);
                        })
                ]),

            Section::make('Content')
                ->columnSpanFull()
                ->schema([
                    Textarea::make('excerpt')
                        ->rows(3)
                        ->label('Excerpt'),
                    RichEditor::make('body')
                        ->label('Body')
                        ->fileAttachmentsDisk('r2')
                        ->fileAttachmentsDirectory('posts/attachments')
                        ->fileAttachmentsVisibility('public')
                        ->columnSpanFull()
                        ->saveUploadedFileAttachmentUsing(function (TemporaryUploadedFile $file): string {
                            return CompressImageService::compress('posts/attachments/', $file);
                        }),
                ]),

            Section::make('Settings')
                ->schema([
                    Select::make('family_id')
                        ->relationship('family', 'name')
                        ->required()
                        ->searchable(),
                    Select::make('author_user_id')
                        ->relationship('author', 'name')
                        ->required()
                        ->searchable(),
                    Select::make('visibility')
                        ->options(collect(PostVisibility::cases())->mapWithKeys(fn ($visibility) => [$visibility->value => ucfirst($visibility->value)])->all())
                        ->default(PostVisibility::FAMILY->value)
                        ->required(),
                    Toggle::make('pinned')
                        ->label('Pinned')
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_path')
                    ->label('Cover')
                    ->disk('r2')
                    ->square(),
                Tables\Columns\TextColumn::make('excerpt')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('family.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('visibility')
                    ->badge(),
                Tables\Columns\IconColumn::make('pinned')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('visibility')
                    ->options(collect(PostVisibility::cases())->mapWithKeys(fn ($visibility) => [$visibility->value => ucfirst($visibility->value)])->all()),
                Tables\Filters\TernaryFilter::make('pinned'),
            ])
            ->recordActions([
                ViewAction::make(),
                FilamentEditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => Pages\ViewPost::route('/{record}'),
        ];
    }
}
