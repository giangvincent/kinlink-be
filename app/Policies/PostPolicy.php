<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Enums\PostVisibility;
use App\Models\Post;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class PostPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::GUEST);
    }

    public function view(User $user, Post $post): bool
    {
        $role = $this->userRole($user, $post);

        if ($role === null) {
            return false;
        }

        return match ($post->visibility) {
            PostVisibility::FAMILY => $role->atLeast(FamilyRole::MEMBER),
            PostVisibility::GUESTS => $role->atLeast(FamilyRole::GUEST),
        };
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::MEMBER);
    }

    public function update(User $user, Post $post): bool
    {
        if ($this->hasMinimumRole($user, FamilyRole::ELDER, $post)) {
            return true;
        }

        return $post->author_user_id === $user->getKey()
            && $this->hasMinimumRole($user, FamilyRole::MEMBER, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        if ($this->hasMinimumRole($user, FamilyRole::ELDER, $post)) {
            return true;
        }

        return $post->author_user_id === $user->getKey()
            && $this->hasMinimumRole($user, FamilyRole::MEMBER, $post);
    }
}
