<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

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

        $contact = ContactMessage::create([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'],
            'email'   => $validated['email'],
            'topic'   => $validated['topic'] ?? 'other',
            'message' => $validated['message'] ?? null,
            'status'  => 'unread',
        ]);

        // Notifikasi ke Role Terkait (Mechanic untuk workshop, Sales untuk showroom/umum, + Super Admin)
        if (($validated['topic'] ?? '') === 'workshop') {
            $rolesToNotify = ['mechanic', 'super-admin'];
        } else {
            $rolesToNotify = ['sales', 'super-admin'];
        }

        $this->notificationService->notifyRoles($rolesToNotify, 'contact_message', [
            'title'        => "Pertanyaan Customer Baru: {$contact->name}",
            'name'         => $contact->name,
            'topic'        => strtoupper($contact->topic),
            'preview'      => $contact->message ?? 'Pertanyaan masuk tanpa catatan pesan.',
            'message_text' => $contact->message,
            'action_url'   => route('admin.contact-messages.show', $contact->id),
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'Terima kasih, ' . $validated['name'] . '! Pesan Anda telah kami terima. Tim Sales / Advisor AUDrive akan segera merespons Anda.');
    }
}
