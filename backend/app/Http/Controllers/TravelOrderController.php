<?php

namespace App\Http\Controllers;

use App\Enums\TravelOrderStatus;
use App\Http\Requests\TravelOrder\StoreTravelOrderRequest;
use App\Http\Requests\TravelOrder\UpdateTravelOrderStatusRequest;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class TravelOrderController extends Controller
{
  public function __construct(
    private TravelOrderService $service
  ) {
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'origin' => 'required|string|max:255',
      'destination' => 'required|string|max:255',
      'travel_date' => 'required|date|after:now',
    ]);

    $order = $this->service->create($data);

    return response()->json($order, 201);
  }

  public function updateStatus(Request $request)
  {
    $data = $request->validate([
      'id' => 'required|string|max:255',
      'status' => 'required|string',
    ]);

    try {
      $status = TravelOrderStatus::from($data['status']);

      $order = TravelOrder::findOrFail($data['id']);
      $updatedOrder = $this->service->updateStatus($order, $status);

      return $this->success($updatedOrder, 'Status do pedido, atualizado com sucesso!');

    } catch (\ValueError $e) {

      return $this->error(
        'Invalid status value',
        ['status' => 'Status must be one of: ' . implode(', ', TravelOrderStatus::cases())]
      );

    } catch (ValidationException $e) {
      return $this->error(
        'Business rule violation',
        $e->errors()
      );
    }
  }

  public function index()
  {
    return response()->json(
      $this->service->list()
    );
  }
}