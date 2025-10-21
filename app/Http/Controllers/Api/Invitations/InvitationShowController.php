<?php

namespace App\Http\Controllers\Api\Invitations;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\FamilyResource;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use Illuminate\Http\Request;

class InvitationShowController extends ApiController
{
    public function __invoke(Request $request, string $token)
    {
        if ($request->has('signature') && ! $request->hasValidSignature()) {
            return $this->fail(['message' => ['Invalid or expired invitation link.']], 403);
        }

        $invitation = Invitation::with('family')->where('token', $token)->firstOrFail();

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return $this->fail(['message' => ['Invitation has expired.']], 410);
        }

        return $this->ok([
            'invitation' => (new InvitationResource($invitation))->toArray($request),
            'family' => (new FamilyResource($invitation->family))->toArray($request),
        ]);
    }
}
