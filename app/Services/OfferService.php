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
    public function create(Customer $customer, CarUnit $carUnit, ?int $amountIdr = null, ?string $note = null): Offer
    {
        // Validasi: unit harus available
        if ($carUnit->status !== CarUnitStatus::Available) {
            throw new \InvalidArgumentException('Unit tidak tersedia untuk dipesan.');
        }

        $amount = $amountIdr ?: $carUnit->price_sell_idr;

        return DB::transaction(function () use ($customer, $carUnit, $amount, $note) {
            // Ketika customer memesan, status unit mobil langsung berubah menjadi Reserved (Dipesan)
            $carUnit->update(['status' => CarUnitStatus::Reserved]);

            return Offer::create([
                'car_unit_id' => $carUnit->id,
                'customer_id' => $customer->id,
                'amount_idr' => $amount,
                'status' => OfferStatus::Pending,
                'note' => $note,
                'expires_at' => now()->addDays(7),
            ]);
        });
    }

    public function accept(Offer $offer, int $reviewedBy): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Pesanan tidak dalam status pending.');
        }

        return DB::transaction(function () use ($offer, $reviewedBy) {
            // Update offer status
            $offer->update([
                'status' => OfferStatus::Accepted,
                'reviewed_by' => $reviewedBy,
            ]);

            // Pastikan unit status tetap / menjadi reserved
            $offer->carUnit->update(['status' => CarUnitStatus::Reserved]);

            // Buat sales order
            $this->createSalesOrderFromOffer($offer);

            return $offer;
        });
    }

    public function reject(Offer $offer, int $reviewedBy, ?string $reason = null): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Pesanan tidak dalam status pending.');
        }

        return DB::transaction(function () use ($offer, $reviewedBy, $reason) {
            $offer->update([
                'status' => OfferStatus::Rejected,
                'reviewed_by' => $reviewedBy,
                'note' => $offer->note.($reason ? "\n\nAlasan penolakan: {$reason}" : ''),
            ]);

            // Jika pesanan ditolak sales, kembalikan status mobil ke Available (Tersedia)
            if ($offer->carUnit && $offer->carUnit->status === CarUnitStatus::Reserved) {
                $offer->carUnit->update(['status' => CarUnitStatus::Available]);
            }

            return $offer->fresh();
        });
    }

    public function cancel(Offer $offer): Offer
    {
        if ($offer->status !== OfferStatus::Pending) {
            throw new \InvalidArgumentException('Hanya pesanan pending yang bisa dibatalkan.');
        }

        return DB::transaction(function () use ($offer) {
            $offer->update(['status' => OfferStatus::Cancelled]);

            // Jika pesanan dibatalkan, kembalikan status mobil ke Available (Tersedia)
            if ($offer->carUnit && $offer->carUnit->status === CarUnitStatus::Reserved) {
                $offer->carUnit->update(['status' => CarUnitStatus::Available]);
            }

            return $offer->fresh();
        });
    }

    public function checkExpiredOffers(): void
    {
        $expiredOffers = Offer::query()
            ->where('status', OfferStatus::Pending)
            ->where('expires_at', '<', now())
            ->with('carUnit')
            ->get();

        foreach ($expiredOffers as $offer) {
            DB::transaction(function () use ($offer) {
                $offer->update(['status' => OfferStatus::Expired]);

                if ($offer->carUnit && $offer->carUnit->status === CarUnitStatus::Reserved) {
                    $offer->carUnit->update(['status' => CarUnitStatus::Available]);
                }
            });
        }
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
            $code = 'SO-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -6));
        } while (SalesOrder::where('code', $code)->exists());

        return $code;
    }
}
