<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\FamilyResource;
use Illuminate\Http\Request;

class FamilyIndexController extends ApiController
{
    public function __invoke(Request $request)
    {
        $families = $request->user()
            ->families()
            ->withPivot('role')
            ->withCount('members')
            ->get();

        return $this->ok(FamilyResource::collection($families));
    }
}
