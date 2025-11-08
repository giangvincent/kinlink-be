<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    public function __invoke(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'locale' => $data['locale'] ?? 'en',
            'time_zone' => $data['time_zone'] ?? config('app.timezone', 'UTC'),
        ]);

        $token = $user->createToken($request->input('device_name', 'api'))->plainTextToken;
        $user->loadMissing('media');

        return $this->created([
            'user' => (new UserResource($user))->toArray($request),
            'token' => $token,
        ]);
    }
}
