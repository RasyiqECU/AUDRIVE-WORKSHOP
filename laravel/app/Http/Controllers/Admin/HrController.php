<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;


class HrController extends Controller
{
    /**
     * Display HR dashboard
     */
    public function index(): View
    {
        return view('admin.hr.index');
    }
}
