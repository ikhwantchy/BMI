<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('system_admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('members.view');
    }

    public function view(User $user, Member $member): bool
    {
        return $user->hasPermissionTo('members.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('members.create');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->hasPermissionTo('members.update');
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->hasPermissionTo('members.delete');
    }
}
