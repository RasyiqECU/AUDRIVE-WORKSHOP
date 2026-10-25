<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\CarUnitStatus;
use App\Enums\OfferStatus;
use App\Enums\SalesOrderStatus;
use App\Enums\SalesOrderType;
use App\Models\CarUnit;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Support\Facades\DB;


class OfferService
{
    public function create(Customer $customer, CarUnit $carUnit, int $amountIdr, ?string $note = null): Offer
    {
        // Validasi: unit harus available
        if ($carUnit->status !== CarUnitStatus::Available) {
            throw new \InvalidArgumentException('Unit tidak tersedia untuk penawaran.');
        }


        return Offer::create([
            'car_unit_id' => $carUnit->id,
            'customer_id' => $customer->id,
            'amount_idr' => $amountIdr,
            'status' => OfferStatus::Pending,
            'note' => $note,
            'expires_at' => now()->addDays(7),
        ]);
    }


    public function accept(Offer $offer, int $reviewedBy): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Offer tidak dalam status pending.');
        }


        return DB::transaction(function () use ($offer, $reviewedBy) {
            // Update offer status
            $offer->update([
                'status' => OfferStatus::Accepted,
                'reviewed_by' => $reviewedBy,
            ]);


            // Update unit status menjadi reserved
            $offer->carUnit->update(['status' => CarUnitStatus::Reserved]);


            // Buat sales order
            $this->createSalesOrderFromOffer($offer);


            return $offer;
        });
    }


    public function reject(Offer $offer, int $reviewedBy, ?string $reason = null): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Offer tidak dalam status pending.');
        }


        $offer->update([
            'status' => OfferStatus::Rejected,
            'reviewed_by' => $reviewedBy,
            'note' => $offer->note . ($reason ? "\n\nAlasan penolakan: {$reason}" : ''),
        ]);


        return $offer->fresh();
    }


    public function cancel(Offer $offer): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Hanya offer pending yang bisa dibatalkan.');
        }


        $offer->update(['status' => OfferStatus::Cancelled]);


        return $offer->fresh();
    }


    public function checkExpiredOffers(): void
    {
        Offer::query()
            ->where('status', OfferStatus::Pending)
            ->where('expires_at', '<', now())
            ->update(['status' => OfferStatus::Expired]);
    }


    protected function createSalesOrderFromOffer(Offer $offer): SalesOrder
    {
        $salesOrder = SalesOrder::create([
            'code' => $this->generateSalesOrderCode(),
            'customer_id' => $offer->customer_id,
            'type' => SalesOrderType::CarSales,
            'status' => SalesOrderStatus::Pending,
            'subtotal_idr' => $offer->amount_idr,
            'discount_idr' => 0,
            'tax_idr' => 0,
            'total_idr' => $offer->amount_idr,
            'paid_idr' => 0,
            'payment_status' => 'pending',
            'notes' => "Dibuat dari offer #{$offer->id}",
            'created_by' => $offer->reviewed_by,
        ]);


        // Buat sales order item
        SalesOrderItem::create([
            'sales_order_id' => $salesOrder->id,
            'item_type' => 'car',
            'car_unit_id' => $offer->car_unit_id,
            'description' => $offer->carUnit->full_name,
            'qty' => 1,
            'unit_price_idr' => $offer->amount_idr,
            'subtotal_idr' => $offer->amount_idr,
        ]);


        return $salesOrder;
    }


    protected function generateSalesOrderCode(): string
    {
        do {
            $code = 'SO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (SalesOrder::where('code', $code)->exists());


        return $code;
    }
}



