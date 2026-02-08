<?php

namespace App\Http\Controllers\Admin;

use App\Models\SoldPackage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\NotificationLog;
use App\Http\Controllers\Controller;
use App\Models\OwnerLogin;

class ReportController extends Controller
{
    public function transaction(Request $request, $userId = null)
    {
        $pageTitle = 'Transaction Logs';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::searchable(['trx', 'owner:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('owner');
        if ($userId) {
            $transactions = $transactions->where('owner_id', $userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'Owner Login History';
        $loginLogs = OwnerLogin::orderBy('id', 'desc')->searchable(['owner:username'])->dateFilter()->with('owner')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = OwnerLogin::where('owner_ip', $ip)->orderBy('id', 'desc')->with('owner')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc')->searchable(['owner:username'])->dateFilter()->with('owner')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function sales()
    {
        $pageTitle = 'Sales History';
        $sales = SoldPackage::searchable(['order_number', 'owner:username'])
            ->with(['owner', 'deposit'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.reports.sales', compact('pageTitle', 'sales'));
    }

    public function b2cPerformance()
    {
        $pageTitle = 'B2C Platform Performance';
        
        // Use Transaction table to aggregate commissions (charge) for B2C sales
        $commissions = Transaction::where('remark', 'b2c_ticket_sale')
            ->selectRaw('SUM(amount) as total_volume, SUM(charge) as total_commission, count(id) as total_bookings')
            ->first();

        $transactions = Transaction::where('remark', 'b2c_ticket_sale')
            ->with('owner')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.reports.b2c_performance', compact('pageTitle', 'commissions', 'transactions'));
    }

    public function tripFeedback()
    {
        $pageTitle = 'Passenger Feedbacks';
        $remarks = \App\Models\TripRating::distinct('rating')->orderBy('rating')->get('rating');
        $feedbacks = \App\Models\TripRating::with(['passenger', 'trip', 'trip.owner'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.feedback', compact('pageTitle', 'feedbacks', 'remarks'));
    }

    public function revenueLedger(Request $request)
    {
        $pageTitle = 'Revenue Ledger (Commissions)';
        $transactions = Transaction::where('remark', 'b2c_ticket_sale')
            ->searchable(['trx', 'owner:username'])
            ->dateFilter()
            ->with('owner')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        
        $totalCommission = Transaction::where('remark', 'b2c_ticket_sale')
            ->dateFilter()
            ->sum('charge');

        return view('admin.reports.revenue_ledger', compact('pageTitle', 'transactions', 'totalCommission'));
    }

    public function settlementLedger(Request $request)
    {
        $pageTitle = 'Operator Settlement Ledger';
        $owners = \App\Models\Owner::searchable(['username', 'email'])
            ->withSum(['transactions as total_operator_earnings' => function($query) {
                $query->where('remark', 'b2c_ticket_sale');
            }], 'amount')
            ->withSum(['transactions as total_commission' => function($query) {
                $query->where('remark', 'b2c_ticket_sale');
            }], 'charge')
            ->withSum(['transactions as total_payouts' => function($query) {
                $query->where('remark', 'withdraw');
            }], 'amount')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('admin.reports.settlement_ledger', compact('pageTitle', 'owners'));
    }
}
