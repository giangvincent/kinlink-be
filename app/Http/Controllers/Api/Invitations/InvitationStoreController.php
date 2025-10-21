<?php

namespace App\Http\Controllers\Api\Invitations;

use App\Enums\InvitationRole;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Invitations\InvitationStoreRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Family;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Support\FamilyContext;

class InvitationStoreController extends ApiController
{
    public function __construct(private readonly InvitationService $invitationService)
    {
    }

    public function __invoke(InvitationStoreRequest $request, FamilyContext $familyContext)
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            return $this->fail(['message' => ['Family context not set.']], 400);
        }

        $this->authorize('create', Invitation::class);

        $family = Family::findOrFail($familyId);

        $invitation = $this->invitationService->createInvitation(
            $family,
            $request->input('email'),
            InvitationRole::from($request->input('role')),
            $request->user()
        );

        return $this->created(new InvitationResource($invitation));
    }
}
