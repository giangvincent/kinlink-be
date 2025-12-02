<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Search\FamilySearchRequest;
use App\Http\Resources\FamilyResource;
use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Collection;
use Throwable;

class FamilySearchController extends ApiController
{
    public function __invoke(FamilySearchRequest $request)
    {
        $user = $request->user();
        $query = $request->validated()['query'];
        $results = collect();

        if (config('scout.driver') && config('scout.driver') !== 'null') {
            try {
                $results = Family::search($query)
                    ->take(15)
                    ->get();

                if ($results->isNotEmpty()) {
                    $results = $this->withMembership($results, $user, 15);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if ($results->isEmpty()) {
            $results = $user->families()
                ->withPivot('role')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('slug', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        return $this->ok(FamilyResource::collection($results));
    }

    private function withMembership(Collection $families, User $user, int $limit): Collection
    {
        $familyIds = $families->pluck('id')->all();

        if ($familyIds === []) {
            return collect();
        }

        $membership = $user->families()
            ->withPivot('role')
            ->whereIn('families.id', $familyIds)
            ->get()
            ->keyBy('id');

        return collect($familyIds)
            ->map(fn (int $familyId) => $membership->get($familyId))
            ->filter()
            ->take($limit)
            ->values();
    }
}
