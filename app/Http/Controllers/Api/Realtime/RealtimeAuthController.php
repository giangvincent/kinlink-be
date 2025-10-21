<?php

namespace App\Http\Controllers\Api\Realtime;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Http\Request;

class RealtimeAuthController extends ApiController
{
    public function __construct(private readonly BroadcastManager $broadcastManager)
    {
    }

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'channel' => ['required', 'string'],
            'socket_id' => ['required', 'string'],
        ]);

        $request->merge([
            'channel_name' => $validated['channel'],
            'socket_id' => $validated['socket_id'],
        ]);

        $response = $this->broadcastManager->auth($request);

        return $this->ok(json_decode($response->getContent(), true));
    }
}
