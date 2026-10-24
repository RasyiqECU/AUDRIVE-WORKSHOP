<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\ApprovalRequest;
use App\Models\User;
use App\Services\Notification\Contracts\NotificationServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    protected ?NotificationServiceContract $notificationService = null;

    public function __construct(?NotificationServiceContract $notificationService = null)
    {
        $this->notificationService = $notificationService ?? app(NotificationServiceContract::class);
    }

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

            // Notifikasi khusus ke Manager dan Super Admin
            try {
                $this->notificationService?->notifyRoles(['manager', 'super-admin'], 'approval_requested', [
                    'title'          => "Pengajuan Approval Baru: " . strtoupper(str_replace('_', ' ', $type)),
                    'message'        => "Pengajuan approval " . strtoupper(str_replace('_', ' ', $type)) . " diajukan oleh {$requester->name}. Menunggu evaluasi Anda.",
                    'type'           => strtoupper(str_replace('_', ' ', $type)),
                    'approval_id'    => $approval->id,
                    'requester_name' => $requester->name,
                    'action_url'     => route('admin.approvals.show', $approval->id),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval request: ' . $e->getMessage());
            }

            return $approval;
        });
    }

    /**
     * Approve request
     */
    public function approve(ApprovalRequest $approval, User $approver, ?string $notes = null): ApprovalRequest
    {
        return DB::transaction(function () use ($approval, $approver, $notes) {
            if ($approval->status !== 'pending' && $approval->status?->value !== 'pending') {
                throw new \Exception('Approval request is not pending');
            }

            $approval->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'decided_at' => now(),
                'reason' => $notes,
            ]);

            // Execute approval action based on type
            $this->executeApprovalAction($approval);

            Log::info('Approval request approved', [
                'approval_id' => $approval->id,
                'approved_by' => $approver->id,
            ]);

            // Notifikasi balik ke staf yang mengajukan
            try {
                if ($approval->requester) {
                    $this->notificationService?->send($approval->requester, 'approval_resolved', [
                        'title'         => "Approval Disetujui: " . strtoupper(str_replace('_', ' ', $approval->type)),
                        'message'       => "Pengajuan approval #" . $approval->id . " (" . strtoupper(str_replace('_', ' ', $approval->type)) . ") telah DISETUJUI oleh " . $approver->name . ($notes ? ". Catatan: {$notes}" : "."),
                        'status'        => 'DISETUJUI',
                        'approval_id'   => $approval->id,
                        'approver_name' => $approver->name,
                        'action_url'    => route('admin.approvals.show', $approval->id),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval approved: ' . $e->getMessage());
            }

            return $approval;
        });
    }

    /**
     * Reject request
     */
    public function reject(ApprovalRequest $approval, User $rejector, ?string $notes = null): ApprovalRequest
    {
        return DB::transaction(function () use ($approval, $rejector, $notes) {
            if ($approval->status !== 'pending' && $approval->status?->value !== 'pending') {
                throw new \Exception('Approval request is not pending');
            }

            $approval->update([
                'status' => 'rejected',
                'approved_by' => $rejector->id,
                'decided_at' => now(),
                'reason' => $notes,
            ]);

            Log::info('Approval request rejected', [
                'approval_id' => $approval->id,
                'rejected_by' => $rejector->id,
            ]);

            // Notifikasi balik ke staf yang mengajukan
            try {
                if ($approval->requester) {
                    $this->notificationService?->send($approval->requester, 'approval_resolved', [
                        'title'         => "Approval Ditolak: " . strtoupper(str_replace('_', ' ', $approval->type)),
                        'message'       => "Pengajuan approval #" . $approval->id . " (" . strtoupper(str_replace('_', ' ', $approval->type)) . ") DITOLAK oleh " . $rejector->name . ($notes ? ". Catatan: {$notes}" : "."),
                        'status'        => 'DITOLAK',
                        'approval_id'   => $approval->id,
                        'approver_name' => $rejector->name,
                        'action_url'    => route('admin.approvals.show', $approval->id),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval rejected: ' . $e->getMessage());
            }

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



