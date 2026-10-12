<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SparePart;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PurchaseOrderService
{
    public function __construct(
        protected StockService $stockService
    ) {}


    public function create(array $data, array $items): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $items) {
            // Create purchase order
            $po = PurchaseOrder::create([
                'code' => $this->generateCode(),
                'supplier_name' => $data['supplier_name'],
                'status' => PurchaseOrderStatus::Draft,
                'total_idr' => 0,
                'ordered_at' => null,
                'received_at' => null,
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);


            // Create items
            $total = 0;
            foreach ($items as $item) {
                $subtotal = $item['qty'] * $item['cost_idr'];


                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'spare_part_id' => $item['spare_part_id'],
                    'qty' => $item['qty'],
                    'cost_idr' => $item['cost_idr'],
                    'subtotal_idr' => $subtotal,
                    'received_qty' => 0,
                ]);


                $total += $subtotal;
            }


            // Update total
            $po->update(['total_idr' => $total]);


            return $po;
        });
    }


    public function send(PurchaseOrder $po): PurchaseOrder
    {
        if ($po->status !== PurchaseOrderStatus::Draft) {
            throw new \Exception('Hanya PO draft yang bisa dikirim.');
        }


        return $po->update([
            'status' => PurchaseOrderStatus::Sent,
            'ordered_at' => now(),
        ]);
    }


    public function receive(PurchaseOrder $po, array $receivedItems): PurchaseOrder
    {
        return DB::transaction(function () use ($po, $receivedItems) {
            if (!in_array($po->status, [PurchaseOrderStatus::Sent, PurchaseOrderStatus::PartiallyReceived])) {
                throw new \Exception('PO harus dalam status sent atau partially received.');
            }


            $allReceived = true;


            foreach ($receivedItems as $itemId => $receivedQty) {
                $item = PurchaseOrderItem::findOrFail($itemId);


                if ($item->purchase_order_id !== $po->id) {
                    throw new \Exception('Item tidak belongs to this PO.');
                }


                $newReceivedQty = $item->received_qty + $receivedQty;


                if ($newReceivedQty > $item->qty) {
                    throw new \Exception("Qty yang diterima melebihi qty order untuk item #{$item->id}.");
                }


                // Update received qty
                $item->update(['received_qty' => $newReceivedQty]);


                // Add stock
                $part = SparePart::find($item->spare_part_id);
                $this->stockService->stockIn(
                    $part,
                    $receivedQty,
                    PurchaseOrder::class,
                    $po->id,
                    "Received from PO #{$po->code}"
                );


                if ($newReceivedQty < $item->qty) {
                    $allReceived = false;
                }
            }


            // Update PO status
            $newStatus = $allReceived ? PurchaseOrderStatus::Received : PurchaseOrderStatus::PartiallyReceived;
            $po->update([
                'status' => $newStatus,
                'received_at' => $allReceived ? now() : $po->received_at,
            ]);


            return $po;
        });
    }


    public function cancel(PurchaseOrder $po): PurchaseOrder
    {
        if (in_array($po->status, [PurchaseOrderStatus::Received, PurchaseOrderStatus::Cancelled])) {
            throw new \Exception('PO tidak bisa dibatalkan.');
        }


        return $po->update(['status' => PurchaseOrderStatus::Cancelled]);
    }


    protected function generateCode(): string
    {
        do {
            $code = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (PurchaseOrder::where('code', $code)->exists());


        return $code;
    }
}



