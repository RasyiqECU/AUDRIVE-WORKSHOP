<?php


declare(strict_types=1);


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AuditService
{
    /**
     * Log audit activity
     */
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null,
        ?string $description = null
    ): void {
        DB::table('audit_logs')->insert([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Log::info('Audit log created', [
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'user_id' => $user?->id ?? auth()->id(),
        ]);
    }


    /**
     * Log user login
     */
    public function logLogin(User $user): void
    {
        $this->log(
            action: 'login',
            modelType: 'user',
            modelId: $user->id,
            user: $user,
            description: "User {$user->name} logged in"
        );
    }


    /**
     * Log user logout
     */
    public function logLogout(User $user): void
    {
        $this->log(
            action: 'logout',
            modelType: 'user',
            modelId: $user->id,
            user: $user,
            description: "User {$user->name} logged out"
        );
    }


    /**
     * Log model created
     */
    public function logCreated(string $modelType, int $modelId, array $values, ?User $user = null): void
    {
        $this->log(
            action: 'created',
            modelType: $modelType,
            modelId: $modelId,
            newValues: $values,
            user: $user,
            description: "Created {$modelType} #{$modelId}"
        );
    }


    /**
     * Log model updated
     */
    public function logUpdated(
        string $modelType,
        int $modelId,
        array $oldValues,
        array $newValues,
        ?User $user = null
    ): void {
        $this->log(
            action: 'updated',
            modelType: $modelType,
            modelId: $modelId,
            oldValues: $oldValues,
            newValues: $newValues,
            user: $user,
            description: "Updated {$modelType} #{$modelId}"
        );
    }


    /**
     * Log model deleted
     */
    public function logDeleted(string $modelType, int $modelId, array $values, ?User $user = null): void
    {
        $this->log(
            action: 'deleted',
            modelType: $modelType,
            modelId: $modelId,
            oldValues: $values,
            user: $user,
            description: "Deleted {$modelType} #{$modelId}"
        );
    }


    /**
     * Log price change
     */
    public function logPriceChange(
        string $modelType,
        int $modelId,
        int $oldPrice,
        int $newPrice,
        ?User $user = null
    ): void {
        $this->log(
            action: 'price_changed',
            modelType: $modelType,
            modelId: $modelId,
            oldValues: ['price' => $oldPrice],
            newValues: ['price' => $newPrice],
            user: $user,
            description: "Price changed from Rp " . number_format($oldPrice) . " to Rp " . number_format($newPrice)
        );
    }


    /**
     * Log approval action
     */
    public function logApproval(
        string $action,
        int $approvalId,
        ?User $user = null
    ): void {
        $this->log(
            action: $action,
            modelType: 'approval_request',
            modelId: $approvalId,
            user: $user,
            description: "Approval #{$approvalId} {$action}"
        );
    }


    /**
     * Log stock adjustment
     */
    public function logStockAdjustment(
        int $sparePartId,
        int $oldStock,
        int $newStock,
        ?User $user = null
    ): void {
        $this->log(
            action: 'stock_adjusted',
            modelType: 'spare_part',
            modelId: $sparePartId,
            oldValues: ['stock' => $oldStock],
            newValues: ['stock' => $newStock],
            user: $user,
            description: "Stock adjusted from {$oldStock} to {$newStock}"
        );
    }


    /**
     * Log payment
     */
    public function logPayment(
        int $salesOrderId,
        int $amount,
        string $method,
        ?User $user = null
    ): void {
        $this->log(
            action: 'payment_received',
            modelType: 'sales_order',
            modelId: $salesOrderId,
            newValues: [
                'amount' => $amount,
                'method' => $method,
            ],
            user: $user,
            description: "Payment received: Rp " . number_format($amount) . " via {$method}"
        );
    }


    /**
     * Get audit logs
     */
    public function getLogs(
        ?string $action = null,
        ?string $modelType = null,
        ?int $userId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        int $limit = 50
    ): Collection {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name');


        if ($action) {
            $query->where('audit_logs.action', $action);
        }


        if ($modelType) {
            $query->where('audit_logs.model_type', $modelType);
        }


        if ($userId) {
            $query->where('audit_logs.user_id', $userId);
        }


        if ($dateFrom) {
            $query->where('audit_logs.created_at', '>=', $dateFrom);
        }


        if ($dateTo) {
            $query->where('audit_logs.created_at', '<=', $dateTo);
        }


        return $query->orderBy('audit_logs.created_at', 'desc')
            ->limit($limit)
            ->get();
    }


    /**
     * Get audit logs for specific model
     */
    public function getModelLogs(string $modelType, int $modelId): Collection
    {
        return DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name')
            ->where('audit_logs.model_type', $modelType)
            ->where('audit_logs.model_id', $modelId)
            ->orderBy('audit_logs.created_at', 'desc')
            ->get();
    }
}



