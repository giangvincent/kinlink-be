<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends ApiController
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('families');

        return $this->ok([
            'user' => (new UserResource($user))->toArray($request),
            'memberships' => MembershipResource::collection($user->families)->toArray($request),
        ]);
    }
}
