<?php

namespace App\Services;

use App\Enums\FamilyRole;
use App\Enums\InvitationRole;
use App\Models\Family;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function createInvitation(Family $family, string $email, InvitationRole $role, ?User $inviter = null): Invitation
    {
        $token = Str::uuid()->toString();

        $invitation = Invitation::updateOrCreate(
            ['family_id' => $family->getKey(), 'email' => $email],
            [
                'role' => $role,
                'token' => $token,
                'expires_at' => now()->addDays(7),
            ]
        );

        $this->sendInvitationEmail($invitation, $inviter);

        return $invitation;
    }

    public function acceptInvitation(Invitation $invitation, User $user): void
    {
        $family = $invitation->family;
        $role = $invitation->role->toFamilyRole();

        $family->members()->syncWithoutDetaching([
            $user->getKey() => ['role' => $role->value],
        ]);

        if ($role === FamilyRole::OWNER && $family->owner_user_id === null) {
            $family->owner_user_id = $user->getKey();
            $family->save();
        }

        $invitation->accepted_at = now();
        $invitation->save();
    }

    protected function sendInvitationEmail(Invitation $invitation, ?User $inviter = null): void
    {
        $url = URL::temporarySignedRoute(
            'api.invitations.show',
            $invitation->expires_at ?? now()->addDays(7),
            ['token' => $invitation->token]
        );

        $this->mailer->raw(
            "You've been invited to join {$invitation->family->name}. Accept: {$url}",
            static function ($message) use ($invitation, $inviter): void {
                $message
                    ->to($invitation->email)
                    ->subject('Family Invitation')
                    ->replyTo($inviter?->email ?? config('mail.from.address'));
            }
        );
    }
}
