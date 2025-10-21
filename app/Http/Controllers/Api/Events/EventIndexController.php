<?php

namespace App\Http\Controllers\Api\Events;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Events\EventIndexRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Support\FamilyContext;

class EventIndexController extends ApiController
{
    public function __invoke(EventIndexRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $data = $request->validated();

        $events = Event::forFamily($familyId)
            ->when($data['from'] ?? null, fn ($query, $from) => $query->where('date_exact', '>=', $from))
            ->when($data['to'] ?? null, fn ($query, $to) => $query->where('date_exact', '<=', $to))
            ->orderBy('date_exact')
            ->get();

        return $this->ok(EventResource::collection($events));
    }
}
