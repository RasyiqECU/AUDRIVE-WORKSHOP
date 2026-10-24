<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display listing of customer inquiries / contact messages.
     */
    public function index(Request $request): View
    {
        $query = ContactMessage::with('repliedBy')->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter topic
        if ($request->filled('topic')) {
            $query->where('topic', $request->topic);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('message', 'like', "%{$s}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $unreadCount = ContactMessage::where('status', 'unread')->count();
        $repliedCount = ContactMessage::where('status', 'replied')->count();
        $totalCount = ContactMessage::count();

        return view('admin.contact-messages.index', compact('messages', 'unreadCount', 'repliedCount', 'totalCount'));
    }

    /**
     * Display a single contact message detail and response form.
     */
    public function show(ContactMessage $contactMessage): View
    {
        // Tandai telah dibaca jika masih unread
        $contactMessage->markAsRead();
        $contactMessage->load('repliedBy');

        // Cek apakah customer adalah user terdaftar di sistem
        $registeredUser = User::where('email', $contactMessage->email)
            ->orWhere('phone', $contactMessage->phone)
            ->first();

        // Format nomor whatsapp (08xx -> 628xx)
        $cleanPhone = preg_replace('/[^0-9]/', '', $contactMessage->phone);
        if (str_starts_with($cleanPhone, '0')) {
            $waPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '62')) {
            $waPhone = $cleanPhone;
        } else {
            $waPhone = '62' . $cleanPhone;
        }

        // Template sapaan ramah untuk WhatsApp
        $waGreeting = urlencode(
            "Halo {$contactMessage->name}, terima kasih telah menghubungi AUDrive Luxury Automotive mengenai " .
            strtoupper($contactMessage->topic) . ". Saya " . (auth()->user()->name ?? 'Sales Representative') .
            " dari tim Sales AUDrive. Ada yang bisa kami bantu lebih lanjut?"
        );

        $waUrl = "https://wa.me/{$waPhone}?text={$waGreeting}";

        return view('admin.contact-messages.show', compact('contactMessage', 'registeredUser', 'waUrl', 'waPhone'));
    }

    /**
     * Store Sales response / reply to the customer inquiry.
     */
    public function reply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:5000'],
        ], [
            'reply.required' => 'Tuliskan catatan jawaban / respon sales terlebih dahulu.',
            'reply.max'      => 'Jawaban maksimal 5000 karakter.',
        ]);

        $contactMessage->update([
            'reply'      => $validated['reply'],
            'replied_by' => auth()->id(),
            'replied_at' => now(),
            'status'     => 'replied',
        ]);

        // Jika customer memiliki akun terdaftar, kirim notifikasi ke akun customer tersebut
        $registeredUser = User::where('email', $contactMessage->email)
            ->orWhere('phone', $contactMessage->phone)
            ->first();

        if ($registeredUser) {
            $this->notificationService->send($registeredUser, 'contact_reply', [
                'title'      => 'Tanggapan dari Sales AUDrive',
                'message'    => "Sales AUDrive ({auth()->user()->name}) telah menanggapi pesan Anda mengenai " . strtoupper($contactMessage->topic),
                'reply_text' => $validated['reply'],
                'detail'     => $validated['reply'],
                'action_url' => route('contact'),
            ]);
        }

        return redirect()
            ->route('admin.contact-messages.show', $contactMessage)
            ->with('success', 'Tanggapan sales berhasil disimpan dan status pesan diubah menjadi "Dibalas".');
    }

    /**
     * Delete contact message.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', 'Pesan kontak customer berhasil dihapus.');
    }
}
