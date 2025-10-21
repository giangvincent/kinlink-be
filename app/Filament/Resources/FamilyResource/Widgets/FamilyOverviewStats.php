<?php

namespace App\Filament\Resources\FamilyResource\Widgets;

use App\Models\Family;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FamilyOverviewStats extends StatsOverviewWidget
{
    protected static ?string $resource = \App\Filament\Resources\FamilyResource::class;

    protected function getStats(): array
    {
        $family = $this->getRecord();

        if (! $family instanceof Family) {
            return [
                Stat::make('Families', Family::count())
                    ->description('Total families in KinLink'),
                Stat::make('Members', Family::withCount('members')->get()->sum('members_count'))
                    ->description('Aggregate member count'),
                Stat::make('Storage Used (MB)', number_format($this->calculateTotalStorageMb(), 2)),
            ];
        }

        $memberCount = $family->members()->count();
        $storageBytes = Media::query()
            ->where('model_type', Family::class)
            ->where('model_id', $family->getKey())
            ->sum('size');
        $storageMb = round($storageBytes / 1024 / 1024, 2);
        $thirtyDaysAgo = now()->subDays(30);
        $previousCount = $family->members()
            ->wherePivot('created_at', '<', $thirtyDaysAgo)
            ->count();

        $growth = $previousCount === 0
            ? ($memberCount > 0 ? 100 : 0)
            : (($memberCount - $previousCount) / $previousCount) * 100;

        return [
            Stat::make('Members', $memberCount)
                ->description('Total members linked to this family'),
            Stat::make('Storage Used (MB)', number_format($storageMb, 2))
                ->description('Sum of media attached to the family'),
            Stat::make('Member Growth', number_format($growth, 2) . '%')
                ->description('Change in members in the last 30 days'),
        ];
    }

    private function calculateTotalStorageMb(): float
    {
        $bytes = Media::query()
            ->where('model_type', Family::class)
            ->sum('size');

        return round($bytes / 1024 / 1024, 2);
    }
}
