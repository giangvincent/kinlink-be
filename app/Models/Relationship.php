<?php

namespace App\Models;

use App\Enums\RelationshipType;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relationship extends Model
{
    use BelongsToFamily;
    use HasFactory;

    protected $fillable = [
        'family_id',
        'person_id_a',
        'person_id_b',
        'type',
        'certainty',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => RelationshipType::class,
            'certainty' => 'integer',
        ];
    }

    public function personA(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id_a');
    }

    public function personB(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id_b');
    }
}
