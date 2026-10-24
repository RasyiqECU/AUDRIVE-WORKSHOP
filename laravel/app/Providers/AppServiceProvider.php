<?php


declare(strict_types=1);


namespace App\Providers;


use App\Models\AiScan;
use App\Models\Appointment;
use App\Models\ApprovalRequest;
use App\Models\CarUnit;
use App\Models\Offer;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\SupportTicket;
use App\Models\WorkOrder;
use App\Policies\AiScanPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\ApprovalRequestPolicy;
use App\Policies\CarUnitPolicy;
use App\Policies\OfferPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalesOrderPolicy;
use App\Policies\ServiceBookingPolicy;
use App\Policies\SparePartPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\WorkOrderPolicy;
use App\Models\CustomerVehicle;
use App\Policies\CustomerVehiclePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        CarUnit::class         => CarUnitPolicy::class,
        Offer::class           => OfferPolicy::class,
        Appointment::class     => AppointmentPolicy::class,
        ServiceBooking::class  => ServiceBookingPolicy::class,
        WorkOrder::class       => WorkOrderPolicy::class,
        AiScan::class          => AiScanPolicy::class,
        SparePart::class       => SparePartPolicy::class,
        SalesOrder::class      => SalesOrderPolicy::class,
        PurchaseOrder::class   => PurchaseOrderPolicy::class,
        ApprovalRequest::class => ApprovalRequestPolicy::class,
        SupportTicket::class   => SupportTicketPolicy::class,
        CustomerVehicle::class => CustomerVehiclePolicy::class,
    ];

    public function register(): void
    {
        $this->app->singleton(
            \App\Services\Notification\Contracts\NotificationServiceContract::class,
            \App\Services\Notification\NotificationService::class
        );
    }

    public function boot(): void
    {
        $this->registerPolicies();


        // Super-admin bypass semua authorization
        Gate::before(function ($user, $ability) {
            if ($user->role === 'super-admin') {
                return true;
            }
        });

        // Set dynamic app.name from SettingService
        try {
            $appName = \App\Services\SettingService::getName();
            if (!empty($appName)) {
                config(['app.name' => $appName]);
            }
        } catch (\Throwable) {
        }
    }
}




