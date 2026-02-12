<?php

namespace App\Services;

use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class TravelOrderService
{
  public function create(array $data): TravelOrder
  {
    $user = Auth::guard('api')->user();

    if (!$user) {
      throw new HttpException(401, 'Usuário não autenticado');
    }

    return TravelOrder::create([
      'user_id' => $user->id,
      'origin' => $data['origin'],
      'destination' => $data['destination'],
      'travel_date' => $data['travel_date'],
      'status' => TravelOrderStatus::REQUESTED,
    ]);
  }
  public function updateStatus(TravelOrder $order, TravelOrderStatus $status): TravelOrder
  {
    if (
      $order->status === TravelOrderStatus::APPROVED &&
      $status === TravelOrderStatus::CANCELED
    ) {
      throw ValidationException::withMessages([
        'status' => 'Approved orders cannot be canceled.'
      ]);
    }

    $order->update(['status' => $status]);

    return $order;
  }

  public function list(): Collection
  {
    $user = Auth::guard('api')->user();

    return TravelOrder::where('user_id', $user->id)
      ->orderByDesc('created_at')
      ->get();
  }
}
