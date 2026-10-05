<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Semua user yang terautentikasi boleh melihat daftar anggota.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['petugas_lapangan', 'asisten_manajer', 'manajer']);
    }

    public function view(User $user, Member $member): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Hanya petugas lapangan dan ke atas yang bisa membuat anggota baru.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['petugas_lapangan', 'asisten_manajer', 'manajer']);
    }

    public function update(User $user, Member $member): bool
    {
        return in_array($user->role, ['petugas_lapangan', 'asisten_manajer', 'manajer']);
    }

    /**
     * Hanya manajer yang bisa menghapus anggota.
     * OPEN ITEM: detail permission antar level management belum final.
     */
    public function delete(User $user, Member $member): bool
    {
        return $user->isManager();
    }
}
