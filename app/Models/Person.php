<?php

namespace App\Models;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        'is_deceased',
        'visibility',
        'meta',
        'closest_relative_id',
        'closest_relationship',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'death_date' => 'date',
            'is_deceased' => 'boolean',
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

    public function closestRelative(): BelongsTo
    {
        return $this->belongsTo(self::class, 'closest_relative_id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();

        $this
            ->addMediaCollection('photos');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(320)
            ->height(320)
            ->sharpen(10)
            ->performOnCollections('avatar', 'photos')
            ->nonQueued();
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
