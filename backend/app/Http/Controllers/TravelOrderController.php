<?php

namespace App\Http\Controllers;

use App\Enums\TravelOrderStatus;
use App\Http\Requests\TravelOrder\StoreTravelOrderRequest;
use App\Http\Requests\TravelOrder\UpdateTravelOrderStatusRequest;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;

class TravelOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Listar pedidos do usuário autenticado
     * Filtros: status, destino, período (start_date / end_date)
     */
    public function index(Request $request)
    {
        $query = TravelOrder::query()
            ->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $orders = $query
            ->orderByDesc('created_at')
            ->paginate(10);

        return TravelOrderResource::collection($orders);
    }

    /**
     * Criar um novo pedido de viagem
     */
    public function store(StoreTravelOrderRequest $request)
    {
        $order = TravelOrder::create([
            'user_id'        => auth()->id(),
            'requester_name' => $request->requester_name,
            'destination'    => $request->destination,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'status'         => TravelOrderStatus::REQUESTED,
        ]);

        return new TravelOrderResource($order);
    }

    /**
     * Consultar um pedido específico
     */
    public function show(TravelOrder $travelOrder)
    {
        $this->authorize('view', $travelOrder);

        return new TravelOrderResource($travelOrder);
    }

    /**
     * Atualizar status (somente admin)
     */
    public function updateStatus(
        UpdateTravelOrderStatusRequest $request,
        TravelOrder $travelOrder,
        TravelOrderService $service
    ) {
        $this->authorize('updateStatus', $travelOrder);

        $order = $service->updateStatus(
            $travelOrder,
            $request->status
        );

        return new TravelOrderResource($order);
    }
}