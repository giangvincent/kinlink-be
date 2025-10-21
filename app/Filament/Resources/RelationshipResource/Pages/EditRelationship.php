<?php

namespace App\Filament\Resources\RelationshipResource\Pages;

use App\Filament\Resources\RelationshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRelationship extends EditRecord
{
    protected static string $resource = RelationshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
