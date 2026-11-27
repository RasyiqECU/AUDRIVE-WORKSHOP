<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\PaymentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentVerificationController extends Controller
{
    public function __construct(protected PaymentVerificationService $payments) {}

    public function index(): View
    {
        $base = Payment::whereNotNull('proof_path')
            ->where('paymentable_type', SalesOrder::class)
            ->with(['paymentable.customer.user', 'paymentable.items']);

        return view('admin.payment-verifications.index', [
            'waiting' => (clone $base)->where('status', PaymentStatus::WaitingVerification)->oldest('uploaded_at')->get(),
            'history' => (clone $base)->whereNotNull('verified_at')->latest('verified_at')->limit(20)->get(),
        ]);
    }

    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        try {
            $order = $this->payments->approve($payment, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Pembayaran #{$order->code} disetujui.");
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate(
            ['rejection_reason' => 'required|string|max:255'],
            ['rejection_reason.required' => 'Alasan penolakan wajib diisi.']
        );

        try {
            $this->payments->reject($payment, $request->user(), $data['rejection_reason']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bukti ditolak. Customer diminta mengunggah ulang.');
    }
}
