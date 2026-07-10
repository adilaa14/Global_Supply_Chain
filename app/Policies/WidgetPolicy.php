<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DashboardWidget;
use Illuminate\Auth\Access\HandlesAuthorization;

class WidgetPolicy
{
    use HandlesAuthorization;

    public function view(User $user, DashboardWidget $widget)
    {
        return $user->id === $widget->user_id || $user->hasRole('Administrator');
    }

    public function update(User $user, DashboardWidget $widget)
    {
        return $user->id === $widget->user_id || $user->hasRole('Administrator');
    }

    public function delete(User $user, DashboardWidget $widget)
    {
        return $user->id === $widget->user_id || $user->hasRole('Administrator');
    }
}
