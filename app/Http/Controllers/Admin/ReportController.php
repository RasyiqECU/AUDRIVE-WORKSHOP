<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function sales(): View
    {
        return view('admin.reports.sales');
    }

    public function workshop(): View
    {
        return view('admin.reports.workshop');
    }

    public function inventory(): View
    {
        return view('admin.reports.inventory');
    }

    public function finance(): View
    {
        return view('admin.reports.finance');
    }
}
