<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SalesOrderStatus;
use App\Models\ApprovalRequest;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Models\SparePart;
use App\Models\User;
use App\Services\Notification\Contracts\NotificationServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                    'title' => 'Pengajuan Approval Baru: '.strtoupper(str_replace('_', ' ', $type)),
                    'message' => 'Pengajuan approval '.strtoupper(str_replace('_', ' ', $type))." diajukan oleh {$requester->name}. Menunggu evaluasi Anda.",
                    'type' => strtoupper(str_replace('_', ' ', $type)),
                    'approval_id' => $approval->id,
                    'requester_name' => $requester->name,
                    'action_url' => route('admin.approvals.show', $approval->id),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval request: '.$e->getMessage());
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
                        'title' => 'Approval Disetujui: '.strtoupper(str_replace('_', ' ', $approval->type)),
                        'message' => 'Pengajuan approval #'.$approval->id.' ('.strtoupper(str_replace('_', ' ', $approval->type)).') telah DISETUJUI oleh '.$approver->name.($notes ? ". Catatan: {$notes}" : '.'),
                        'status' => 'DISETUJUI',
                        'approval_id' => $approval->id,
                        'approver_name' => $approver->name,
                        'action_url' => route('admin.approvals.show', $approval->id),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval approved: '.$e->getMessage());
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
                        'title' => 'Approval Ditolak: '.strtoupper(str_replace('_', ' ', $approval->type)),
                        'message' => 'Pengajuan approval #'.$approval->id.' ('.strtoupper(str_replace('_', ' ', $approval->type)).') DITOLAK oleh '.$rejector->name.($notes ? ". Catatan: {$notes}" : '.'),
                        'status' => 'DITOLAK',
                        'approval_id' => $approval->id,
                        'approver_name' => $rejector->name,
                        'action_url' => route('admin.approvals.show', $approval->id),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi approval rejected: '.$e->getMessage());
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
        $order = SalesOrder::findOrFail((int) ($payload['sales_order_id'] ?? 0));
        $amount = (int) ($payload['amount_idr'] ?? 0);

        if ($amount <= 0) {
            throw new \Exception('Nominal diskon tidak valid.');
        }

        if ($amount > $order->total_idr) {
            throw new \Exception("Nominal diskon melebihi total nilai pesanan #{$order->code}.");
        }

        $order->increment('discount_idr', $amount);
        $order->decrement('total_idr', $amount);
        $order->refresh();

        Log::info('Approval discount applied', [
            'sales_order_id' => $order->id,
            'amount_idr' => $amount,
            'new_total_idr' => $order->total_idr,
        ]);
    }

    /**
     * Process refund
     */
    protected function processRefund(array $payload): void
    {
        $payment = Payment::findOrFail((int) ($payload['payment_id'] ?? 0));
        $amount = (int) ($payload['amount_idr'] ?? 0);

        if (! $payment->isSuccessful()) {
            throw new \Exception('Pembayaran tidak berstatus Berhasil atau telah direfund sebelumnya.');
        }

        if ($amount <= 0 || $amount > $payment->amount_idr) {
            throw new \Exception("Nominal refund tidak valid. Maksimum: {$payment->amount_idr}");
        }

        // Catatan pembalik dana (jejak audit pembayaran)
        Payment::create([
            'paymentable_type' => $payment->paymentable_type,
            'paymentable_id' => $payment->paymentable_id,
            'method' => 'refund',
            'status' => PaymentStatus::Refunded,
            'amount_idr' => $amount,
            'reference' => 'REF-'.strtoupper(Str::random(10)),
            'paid_at' => now(),
            'metadata' => [
                'refund_of' => $payment->id,
                'reason' => $payload['reason'] ?? null,
            ],
        ]);

        // Tandai pembayaran asli telah dikembalikan
        $payment->update(['status' => PaymentStatus::Refunded]);
        $payment->refresh();

        // Sesuaikan saldo terbayar pada pesanan
        if ($payment->paymentable instanceof SalesOrder) {
            $order = $payment->paymentable;
            $order->decrement('paid_idr', $amount);
            $order->refresh();

            if (! $order->isFullyPaid()) {
                $order->update(['status' => SalesOrderStatus::Pending]);
            }
        }

        Log::info('Approval refund processed', [
            'payment_id' => $payment->id,
            'amount_idr' => $amount,
        ]);
    }

    /**
     * Adjust price
     */
    protected function adjustPrice(array $payload): void
    {
        $part = SparePart::findOrFail((int) ($payload['spare_part_id'] ?? 0));
        $newPrice = (int) ($payload['new_price_idr'] ?? 0);

        if ($newPrice < 0) {
            throw new \Exception('Harga baru tidak valid.');
        }

        if ($newPrice === $part->price_idr) {
            throw new \Exception("Harga {$part->name} sudah {$newPrice}, tidak ada perubahan.");
        }

        $oldPrice = $part->price_idr;
        $part->update(['price_idr' => $newPrice]);

        Log::info('Approval price adjusted', [
            'spare_part_id' => $part->id,
            'old_price_idr' => $oldPrice,
            'new_price_idr' => $newPrice,
        ]);
    }

    /**
     * Adjust stock
     */
    protected function adjustStock(array $payload): void
    {
        $part = SparePart::findOrFail((int) ($payload['spare_part_id'] ?? 0));
        $newStock = (int) ($payload['new_stock'] ?? 0);

        if ($newStock < 0) {
            throw new \Exception('Jumlah stok baru tidak valid.');
        }

        app(StockService::class)->adjustStock(
            $part,
            $newStock,
            'Approved approval stock adjustment'.(isset($payload['reason']) ? ': '.$payload['reason'] : '')
        );

        Log::info('Approval stock adjusted', [
            'spare_part_id' => $part->id,
            'new_stock' => $newStock,
        ]);
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
