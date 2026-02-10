<?php

namespace App\Policies;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TravelOrderPolicy
{
    public function updateStatus(User $user, TravelOrder $order): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, TravelOrder $order): bool
    {
        return $user->id === $order->user_id || $user->is_admin;
    }
}
