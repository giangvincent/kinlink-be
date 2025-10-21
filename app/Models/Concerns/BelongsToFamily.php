<?php

namespace App\Models\Concerns;

use App\Models\Family;
use App\Support\FamilyContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToFamily
{
    protected static function bootBelongsToFamily(): void
    {
        static::addGlobalScope('family_context', function (Builder $builder): void {
            $familyId = app(FamilyContext::class)->currentFamilyId();

            if ($familyId !== null) {
                $builder->where($builder->qualifyColumn('family_id'), $familyId);
            }
        });

        static::creating(function ($model): void {
            if ($model->family_id) {
                return;
            }

            $familyId = app(FamilyContext::class)->currentFamilyId();

            if ($familyId !== null) {
                $model->family_id = $familyId;
            }
        });
    }

    public function scopeForFamily(Builder $builder, Family|int|string $family): Builder
    {
        $familyId = match (true) {
            $family instanceof Family => $family->getKey(),
            is_numeric($family) => (int) $family,
            default => Family::query()->where('slug', $family)->value('id'),
        };

        if ($familyId === null) {
            return $builder->whereRaw('1 = 0');
        }

        return $builder
            ->withoutGlobalScope('family_context')
            ->where($builder->qualifyColumn('family_id'), $familyId);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}
