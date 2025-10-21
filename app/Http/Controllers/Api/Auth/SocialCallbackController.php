<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class SocialCallbackController extends ApiController
{
    public function __invoke(string $provider)
    {
        $provider = strtolower($provider);

        if (! is_array(Config::get("services.{$provider}"))) {
            return $this->fail(['message' => ['Unsupported provider.']], 404);
        }

        try {
            /** @var SocialiteUser $socialUser */
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $th) {
            report($th);

            return $this->fail(['message' => ['Unable to authenticate with provider.']], 400);
        }

        if (! $socialUser->getEmail()) {
            return $this->fail(['message' => ['Provider did not return an email address.']], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'User',
                'password' => Hash::make(Str::password(32)),
                'locale' => app()->getLocale(),
                'time_zone' => config('app.timezone', 'UTC'),
            ]
        );

        $token = $user->createToken('social-'.$provider)->plainTextToken;

        return $this->ok([
            'user' => (new UserResource($user))->toArray(request()),
            'token' => $token,
            'provider' => $provider,
        ]);
    }
}
