<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    public function view(User $user)
    {
        return $user->hasAnyRole(['Administrator', 'Importer', 'Exporter']);
    }

    public function manage(User $user)
    {
        return $user->hasRole('Administrator');
    }
}
