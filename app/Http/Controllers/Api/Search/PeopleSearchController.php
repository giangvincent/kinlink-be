<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\ApiController;
use App\Enums\PersonVisibility;
use App\Http\Requests\Api\Search\PeopleSearchRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Support\FamilyContext;
use Throwable;

class PeopleSearchController extends ApiController
{
    public function __invoke(PeopleSearchRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $query = $request->validated()['query'];
        $results = collect();

        if (config('scout.driver') && config('scout.driver') !== 'null') {
            try {
                $results = Person::search($query)
                    ->where('family_id', $familyId)
                    ->take(15)
                    ->get();
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        if ($results->isEmpty()) {
            $results = Person::forFamily($familyId)
                ->where('visibility', '!=', PersonVisibility::PRIVATE->value)
                ->where(function ($q) use ($query) {
                    $q->where('display_name', 'like', "%{$query}%")
                        ->orWhere('surname', 'like', "%{$query}%");
                })
                ->orderBy('display_name')
                ->limit(15)
                ->get();
        }

        return $this->ok(PersonResource::collection($results));
    }
}
