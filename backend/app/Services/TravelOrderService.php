<?php

namespace App\Services;

use App\Enums\TravelOrderStatus;
use App\Models\TravelOrder;
use Illuminate\Validation\ValidationException;

class TravelOrderService
{
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
}
