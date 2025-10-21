<?php

namespace App\Http\Controllers\Api\Invitations;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Invitations\InvitationAcceptRequest;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Hash;

class InvitationAcceptController extends ApiController
{
    public function __construct(private readonly InvitationService $invitationService)
    {
    }

    public function __invoke(InvitationAcceptRequest $request, string $token)
    {
        $invitation = Invitation::with('family')->where('token', $token)->firstOrFail();

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return $this->fail(['message' => ['Invitation has expired.']], 410);
        }

        $user = $request->user();

        if (! $user) {
            $data = $request->validated();

            if (! isset($data['password'])) {
                return $this->fail(['password' => ['Password is required to accept without an account.']], 422);
            }

            $user = User::firstOrCreate(
                ['email' => $invitation->email],
                [
                    'name' => $data['name'] ?? $invitation->email,
                    'password' => Hash::make($data['password']),
                    'locale' => $invitation->family->locale,
                    'time_zone' => config('app.timezone', 'UTC'),
                ]
            );
        }

        $this->invitationService->acceptInvitation($invitation, $user);

        return $this->ok([
            'invitation' => (new InvitationResource($invitation))->toArray($request),
            'user' => (new UserResource($user))->toArray($request),
        ]);
    }
}
