<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;

class SocialRedirectController extends ApiController
{
    public function __invoke(Request $request, string $provider)
    {
        $provider = strtolower($provider);

        if (! $this->isProviderEnabled($provider)) {
            return $this->fail(['message' => ['Unsupported provider.']], 404);
        }

        $redirectUrl = Socialite::driver($provider)
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return $this->ok([
            'url' => $redirectUrl,
            'provider' => $provider,
        ]);
    }

    protected function isProviderEnabled(string $provider): bool
    {
        $config = Config::get("services.{$provider}");

        if (! is_array($config)) {
            return false;
        }

        return Arr::has($config, ['client_id', 'client_secret']);
    }
}
