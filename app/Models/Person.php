<?php

namespace App\Models;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Person extends Model implements HasMedia
{
    use BelongsToFamily;
    use HasFactory;
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'family_id',
        'given_name',
        'middle_name',
        'surname',
        'display_name',
        'gender',
        'birth_date',
        'death_date',
        'visibility',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
            'meta' => 'array',
            'gender' => PersonGender::class,
            'visibility' => PersonVisibility::class,
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function primaryRelationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'person_id_a');
    }

    public function relatedRelationships(): HasMany
    {
        return $this->hasMany(Relationship::class, 'person_id_b');
    }

    public function shouldBeSearchable(): bool
    {
        return $this->visibility !== PersonVisibility::PRIVATE;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getKey(),
            'family_id' => $this->family_id,
            'given_name' => $this->given_name,
            'middle_name' => $this->middle_name,
            'surname' => $this->surname,
            'display_name' => $this->display_name,
            'full_name' => trim(collect([
                $this->given_name,
                $this->middle_name,
                $this->surname,
            ])->filter()->implode(' ')),
        ];
    }
}
