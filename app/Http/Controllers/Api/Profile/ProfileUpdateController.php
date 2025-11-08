<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileUpdateController extends ApiController
{
    public function __invoke(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $data = collect($request->validated())
            ->except(['avatar', 'clear_avatar'])
            ->toArray();

        $user->fill($data);
        $user->save();

        if ($request->boolean('clear_avatar')) {
            $user->clearMediaCollection('avatar');
        }

        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        $user->loadMissing('media');

        return $this->ok(new UserResource($user));
    }
}
