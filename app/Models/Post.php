<?php

namespace App\Models;

use App\Enums\PostVisibility;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use BelongsToFamily;
    use HasFactory;
    use InteractsWithMedia;
    use Searchable;

    protected $fillable = [
        'family_id',
        'author_user_id',
        'body',
        'visibility',
        'pinned',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => PostVisibility::class,
            'pinned' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getKey(),
            'family_id' => $this->family_id,
            'body' => $this->body,
            'visibility' => $this->visibility?->value,
            'pinned' => $this->pinned,
        ];
    }
}
