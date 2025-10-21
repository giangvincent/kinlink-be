<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipResource;
use Illuminate\Http\Request;

class MembershipIndexController extends ApiController
{
    public function __invoke(Request $request)
    {
        $memberships = $request->user()
            ->families()
            ->withPivot('role')
            ->get();

        return $this->ok(MembershipResource::collection($memberships));
    }
}
