<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\ApprovalRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ApprovalService
{
    /**
     * Create approval request
     */
    public function createRequest(
        string $type,
        array $payload,
        User $requester
    ): ApprovalRequest {
        return DB::transaction(function () use ($type, $payload, $requester) {
            $approval = ApprovalRequest::create([
                'type' => $type,
                'payload' => $payload,
                'requested_by' => $requester->id,
                'status' => 'pending',
            ]);


            Log::info('Approval request created', [
                'approval_id' => $approval->id,
                'type' => $type,
                'requested_by' => $requester->id,
            ]);


            return $approval;
        });
    }


    /**
     * Approve request
     */
    public function approve(ApprovalRequest $approval, User $approver, ?string $notes = null): ApprovalRequest
    {
        return DB::transaction(function () use ($approval, $approver, $notes) {
            if ($approval->status !== 'pending') {
                throw new \Exception('Approval request is not pending');
            }


            $approval->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'notes' => $notes,
            ]);


            // Execute approval action based on type
            $this->executeApprovalAction($approval);


            Log::info('Approval request approved', [
                'approval_id' => $approval->id,
                'approved_by' => $approver->id,
            ]);


            return $approval;
        });
    }


    /**
     * Reject request
     */
    public function reject(ApprovalRequest $approval, User $rejector, ?string $notes = null): ApprovalRequest
    {
        return DB::transaction(function () use ($approval, $rejector, $notes) {
            if ($approval->status !== 'pending') {
                throw new \Exception('Approval request is not pending');
            }


            $approval->update([
                'status' => 'rejected',
                'approved_by' => $rejector->id,
                'approved_at' => now(),
                'notes' => $notes,
            ]);


            Log::info('Approval request rejected', [
                'approval_id' => $approval->id,
                'rejected_by' => $rejector->id,
            ]);


            return $approval;
        });
    }


    /**
     * Execute approval action based on type
     */
    protected function executeApprovalAction(ApprovalRequest $approval): void
    {
        $payload = $approval->payload;


        switch ($approval->type) {
            case 'discount':
                $this->applyDiscount($payload);
                break;


            case 'refund':
                $this->processRefund($payload);
                break;


            case 'price_adjustment':
                $this->adjustPrice($payload);
                break;


            case 'stock_adjustment':
                $this->adjustStock($payload);
                break;


            default:
                Log::warning('Unknown approval type', ['type' => $approval->type]);
        }
    }


    /**
     * Apply discount to sales order
     */
    protected function applyDiscount(array $payload): void
    {
        // Implementation would update sales order with discount
        Log::info('Discount applied', $payload);
    }


    /**
     * Process refund
     */
    protected function processRefund(array $payload): void
    {
        // Implementation would process refund
        Log::info('Refund processed', $payload);
    }


    /**
     * Adjust price
     */
    protected function adjustPrice(array $payload): void
    {
        // Implementation would adjust product/service price
        Log::info('Price adjusted', $payload);
    }


    /**
     * Adjust stock
     */
    protected function adjustStock(array $payload): void
    {
        // Implementation would adjust inventory stock
        Log::info('Stock adjusted', $payload);
    }


    /**
     * Get pending approvals
     */
    public function getPendingApprovals(?string $type = null): Collection
    {
        $query = ApprovalRequest::where('status', 'pending')
            ->with(['requester', 'approver']);


        if ($type) {
            $query->where('type', $type);
        }


        return $query->latest()->get();
    }


    /**
     * Get approval history
     */
    public function getHistory(?string $type = null, int $limit = 50): Collection
    {
        $query = ApprovalRequest::whereIn('status', ['approved', 'rejected'])
            ->with(['requester', 'approver']);


        if ($type) {
            $query->where('type', $type);
        }


        return $query->latest('approved_at')->limit($limit)->get();
    }
}



