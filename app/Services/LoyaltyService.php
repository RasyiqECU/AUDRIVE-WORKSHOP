<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LoyaltyService
{
    /**
     * Calculate points from sales order
     */
    public function calculatePointsFromOrder(SalesOrder $order): int
    {
        // 1 point per Rp 10.000 spent
        return (int) floor($order->total_amount / 10000);
    }


    /**
     * Add points to customer
     */
    public function addPoints(Customer $customer, int $points, string $description, ?SalesOrder $order = null): LoyaltyTransaction
    {
        return DB::transaction(function () use ($customer, $points, $description, $order) {
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'earning',
                'points' => $points,
                'description' => $description,
                'reference_type' => $order ? SalesOrder::class : null,
                'reference_id' => $order?->id,
            ]);


            $customer->increment('loyalty_points', $points);


            Log::info("Loyalty points added", [
                'customer_id' => $customer->id,
                'points' => $points,
                'transaction_id' => $transaction->id,
            ]);


            return $transaction;
        });
    }


    /**
     * Redeem points from customer
     */
    public function redeemPoints(Customer $customer, int $points, string $description, ?string $referenceType = null, ?int $referenceId = null): LoyaltyTransaction
    {
        return DB::transaction(function () use ($customer, $points, $description, $referenceType, $referenceId) {
            if ($customer->loyalty_points < $points) {
                throw new \Exception("Insufficient loyalty points");
            }


            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'type' => 'redemption',
                'points' => -$points, // Negative for redemption
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);


            $customer->decrement('loyalty_points', $points);


            Log::info("Loyalty points redeemed", [
                'customer_id' => $customer->id,
                'points' => $points,
                'transaction_id' => $transaction->id,
            ]);


            return $transaction;
        });
    }


    /**
     * Recalculate customer loyalty points from all transactions
     */
    public function recalculatePoints(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $totalPoints = LoyaltyTransaction::where('customer_id', $customer->id)
                ->sum('points');


            $customer->update(['loyalty_points' => $totalPoints]);


            Log::info("Loyalty points recalculated", [
                'customer_id' => $customer->id,
                'total_points' => $totalPoints,
            ]);
        });
    }


    /**
     * Get loyalty history for customer
     */
    public function getHistory(Customer $customer, int $limit = 20): \Illuminate\Support\Collection
    {
        return LoyaltyTransaction::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }


    /**
     * Get loyalty tier based on points
     */
    public function getTier(Customer $customer): string
    {
        $points = $customer->loyalty_points;


        return match (true) {
            $points >= 10000 => 'Platinum',
            $points >= 5000 => 'Gold',
            $points >= 2000 => 'Silver',
            $points >= 500 => 'Bronze',
            default => 'Basic',
        };
    }


    /**
     * Check if customer has enough points
     */
    public function hasEnoughPoints(Customer $customer, int $points): bool
    {
        return $customer->loyalty_points >= $points;
    }
}



