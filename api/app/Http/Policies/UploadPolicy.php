<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;

class UploadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staf_gudang', 'manajer']);
    }

    public function view(User $user, Upload $upload): bool
    {
        // Admin bisa lihat semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // Manajer bisa lihat semua
        if ($user->hasRole('manajer')) {
            return true;
        }

        // Staf gudang hanya bisa lihat upload sendiri
        if ($user->hasRole('staf_gudang')) {
            return $upload->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staf_gudang']);
    }

    public function delete(User $user, Upload $upload): bool
    {
        // Admin bisa hapus semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // Staf gudang hanya bisa hapus upload sendiri
        if ($user->hasRole('staf_gudang')) {
            return $upload->user_id === $user->id;
        }

        return false;
    }
}