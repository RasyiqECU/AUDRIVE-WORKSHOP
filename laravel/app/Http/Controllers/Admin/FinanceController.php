<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;


class FinanceController extends Controller
{
    protected FinanceService $financeService;


    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }


    /**
     * Display finance dashboard
     */
    public function index(): View
    {
        $today = Carbon::today();
        $dailyReport = $this->financeService->getDailyCashReport($today);
        $monthlyTrend = $this->financeService->getMonthlyRevenueTrend(12);


        return view('admin.finance.index', compact('dailyReport', 'monthlyTrend'));
    }


    /**
     * Display daily cash report
     */
    public function dailyCash(Request $request): View
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $report = $this->financeService->getDailyCashReport(Carbon::parse($date));


        return view('admin.finance.daily-cash', compact('report', 'date'));
    }


    /**
     * Display invoice list
     */
    public function invoices(Request $request): View
    {
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');


        $invoices = $this->financeService->getInvoices($status, $dateFrom, $dateTo);


        return view('admin.finance.invoices', compact('invoices'));
    }


    /**
     * Display payment list
     */
    public function payments(Request $request): View
    {
        $method = $request->input('method');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');


        $payments = $this->financeService->getPayments($method, $dateFrom, $dateTo);


        return view('admin.finance.payments', compact('payments'));
    }


    /**
     * Display profit/loss report
     */
    public function profitLoss(Request $request): View
    {
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()));


        $report = $this->financeService->getProfitLossReport($startDate, $endDate);


        return view('admin.finance.profit-loss', compact('report', 'startDate', 'endDate'));
    }


    /**
     * Display accounts receivable
     */
    public function accountsReceivable(): View
    {
        $receivable = $this->financeService->getAccountsReceivable();


        return view('admin.finance.accounts-receivable', compact('receivable'));
    }


    /**
     * Get finance stats via API
     */
    public function stats()
    {
        $today = Carbon::today();
        $dailyReport = $this->financeService->getDailyCashReport($today);
        $receivable = $this->financeService->getAccountsReceivable();


        return response()->json([
            'daily_total' => $dailyReport['total_all'],
            'receivable_total' => $receivable['total_receivable'],
            'receivable_count' => $receivable['outstanding_count'],
        ]);
    }
}



