<?php

namespace App\Models;

use App\Enums\EventType;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use BelongsToFamily;
    use HasFactory;

    protected $fillable = [
        'family_id',
        'person_id',
        'type',
        'date_exact',
        'date_range',
        'lunar',
        'place',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'date_exact' => 'date',
            'date_range' => 'array',
            'lunar' => 'boolean',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
