<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StorePaymentProofRequest;
use App\Http\Resources\SalesOrderResource;
use App\Models\SalesOrder;
use App\Models\WorkOrderItem;
use App\Services\PaymentSettings;
use App\Services\PaymentVerificationService;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Pesanan & pembayaran transfer milik customer, serta konfirmasi suku cadang. */
class OrderController extends ApiController
{
    public function show(Request $request, SalesOrder $order): JsonResponse
    {
        abort_unless($request->user()->customer && $order->customer_id === $request->user()->customer->id, 403);

        $lastTransfer = $order->payments()->whereNotNull('proof_path')->latest('id')->first();

        return $this->successResponse([
            'order' => new SalesOrderResource($order->load('items')),
            'progress' => $order->progressLabel(),
            'amount_due_idr' => $order->amountDue(),
            'pay_before' => $order->reserved_until?->toIso8601String(),
            'can_upload_proof' => $order->awaitsProof(),
            'rejection_reason' => $lastTransfer?->rejection_reason,
            'bank_accounts' => PaymentSettings::accounts(),
        ]);
    }

    public function uploadProof(StorePaymentProofRequest $request, SalesOrder $order, PaymentVerificationService $payments): JsonResponse
    {
        try {
            $payments->submitProof($order, $request->transferData(), $request->file('proof'));
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), null, 422);
        }

        return $this->successResponse(null, 'Bukti transfer terkirim.', 201);
    }

    public function decidePart(Request $request, WorkOrderItem $item, WorkOrderService $workOrders): JsonResponse
    {
        $customer = $request->user()->customer;
        $workOrder = $item->workOrder;
        $ownerId = $workOrder->vehicle?->customer_id ?? $workOrder->serviceBooking?->customer_id;
        abort_unless($customer && $ownerId === $customer->id, 403);

        $data = $request->validate(['decision' => 'required|in:buy,bring_own,skip', 'note' => 'nullable|string|max:255']);

        if ($data['decision'] === 'bring_own' && $item->install_fee_idr === null) {
            return $this->errorResponse('Biaya jasa pasang belum ditentukan bengkel.', null, 422);
        }

        try {
            $workOrders->decidePart($item, $data['decision'], (int) $item->install_fee_idr, $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), null, 422);
        }

        return $this->successResponse(null, 'Pilihan dicatat.');
    }
}
