<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends ApiController
{
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->fail(['message' => ['Invalid credentials.']], 422);
        }

        $token = $user->createToken($request->input('device_name', 'api'))->plainTextToken;
        $user->loadMissing('media');

        return $this->ok([
            'user' => (new UserResource($user))->toArray($request),
            'token' => $token,
        ]);
    }
}
