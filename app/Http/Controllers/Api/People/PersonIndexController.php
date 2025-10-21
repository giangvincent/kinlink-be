<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\People\PersonIndexRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;

class PersonIndexController extends ApiController
{
    public function __invoke(PersonIndexRequest $request)
    {
        $data = $request->validated();

        $people = Person::query()
            ->when($data['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('display_name', 'like', "%{$search}%")
                        ->orWhere('given_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%");
                });
            })
            ->when($data['surname'] ?? null, fn ($query, $surname) => $query->where('surname', $surname))
            ->when(isset($data['generation']), function ($query) use ($data) {
                $query->where('meta->generation', $data['generation']);
            })
            ->orderBy('display_name')
            ->paginate(25);

        return $this->ok([
            'items' => PersonResource::collection($people->items())->toArray($request),
            'pagination' => [
                'current_page' => $people->currentPage(),
                'per_page' => $people->perPage(),
                'total' => $people->total(),
                'has_more' => $people->hasMorePages(),
                'next_cursor' => $people->nextPageUrl(),
            ],
        ]);
    }
}
