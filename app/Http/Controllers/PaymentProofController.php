<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentProofRequest;
use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\PaymentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    public function store(StorePaymentProofRequest $request, SalesOrder $order, PaymentVerificationService $payments): RedirectResponse
    {
        try {
            $payments->submitProof($order, $request->transferData(), $request->file('proof'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->to($order->customerUrl())
            ->with('success', 'Bukti transfer terkirim. Kasir akan memverifikasi.');
    }

    /** Bukti hanya bisa dilihat pemilik pesanan dan Cashier. */
    public function show(Request $request, Payment $payment): StreamedResponse
    {
        $user = $request->user();
        $isOwner = $user->customer
            && $payment->paymentable instanceof SalesOrder
            && $payment->paymentable->customer_id === $user->customer->id;

        abort_unless($isOwner || in_array($user->role, ['super-admin', 'cashier'], true), 403);
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path, null, ['X-Content-Type-Options' => 'nosniff']);
    }
}
