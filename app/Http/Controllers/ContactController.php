<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Simpan pesan dari formulir kontak publik.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:30'],
            'email'   => ['required', 'email', 'max:255'],
            'topic'   => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:3000'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        ContactMessage::create([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'email'   => $validated['email'],
            'topic'   => $validated['topic'] ?? 'other',
            'message' => $validated['message'] ?? null,
            'status'  => 'unread',
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'Terima kasih, ' . $validated['name'] . '! Pesan Anda telah kami terima. Tim kami akan menghubungi Anda segera.');
    }
}
