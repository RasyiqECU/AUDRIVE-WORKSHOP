<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SalesOrder;
use App\Services\PaymentSettings;
use App\Services\PaymentVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    public function store(Request $request, SalesOrder $order, PaymentVerificationService $payments): RedirectResponse
    {
        abort_unless($request->user()->customer && $order->customer_id === $request->user()->customer->id, 403);

        $data = $request->validate([
            'proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sender_name' => ['required', 'string', 'max:100'],
            'destination_bank' => ['required', Rule::in(array_column(PaymentSettings::accounts(), 'bank'))],
            'amount_idr' => ['required', 'integer', 'min:1'],
        ], [
            'proof.required' => 'Bukti transfer wajib diunggah.',
            'proof.uploaded' => 'Gambar gagal diunggah. Ukuran maksimal 2 MB.',
            'proof.image' => 'Bukti harus berupa gambar.',
            'proof.mimes' => 'Format bukti harus JPG, PNG, atau WEBP.',
            'proof.max' => 'Ukuran bukti maksimal 2 MB.',
        ]);

        try {
            $payments->submitProof($order, [
                'sender_name' => $data['sender_name'],
                'destination_bank' => $data['destination_bank'],
                'amount_idr' => (int) $data['amount_idr'],
            ], $request->file('proof'));
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('parts.orders.show', $order)
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
