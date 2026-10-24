<?php

declare(strict_types=1);

namespace App\Http\Controllers\SparePart;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteRequestController extends Controller
{
    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('parts.index')->with('success', 'Permintaan estimasi harga part berhasil dikirim. Tim kami akan segera menghubungi Anda.');
    }
}
