<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\RegistrationPayment;
use App\Models\MonthlyPayment;
use App\Models\Donation;

class DashboardController extends Controller
{
    public function index()
    {
        $fee = MonthlyPayment::monthlyFee();

        // ── Member Count ──────────────────────────────────────────────
        $totalMembers = Member::count();

        // ── Registration Payments ─────────────────────────────────────
        $pendingRegistration = Member::whereDoesntHave('registrationPayment', fn($q) =>
            $q->where('status', 'paid')
        )->count();

        $registrationIncome = RegistrationPayment::sum('paid_amount');

        // ── Monthly Fees (new transaction-based schema) ───────────────

        // Total collected across ALL monthly payment transactions
        $monthlyIncome = (float) MonthlyPayment::sum('paid_amount');

        // Total due across all members (months × fee per member)
        // We compute this by summing each member's months × fee
        $totalDue = Member::all()->sum(function ($member) use ($fee) {
            return count(MonthlyPayment::allMonthsForMember($member)) * $fee;
        });

        // Outstanding = total due − total collected (floor at 0)
        $monthlyOutstanding = max(0, $totalDue - $monthlyIncome);

        // Counts by status of latest transaction per member
        // A member is "fully paid" if their latest transaction shows paid/overpaid
        $countPaid     = MonthlyPayment::where('status', 'paid')->count();
        $countPartial  = MonthlyPayment::countPartial();
        $countOverpaid = MonthlyPayment::where('status', 'overpaid')->count();
        // Members who have NO payment transaction yet (never paid anything)
        $countNeverPaid = Member::whereDoesntHave('monthlyPayments')->count();

        // ── Donations ─────────────────────────────────────────────────
        $totalDonations = (float) Donation::where('status', 'received')->sum('amount');

        // ── Total Income ──────────────────────────────────────────────
        $totalIncome = $registrationIncome + $monthlyIncome + $totalDonations;

        // ── Recent Monthly Transactions (latest 5) ────────────────────
        $recentMonthlyPayments = MonthlyPayment::with('member')
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // ── Recent Members (latest 6) ─────────────────────────────────
        $recentMembers = Member::with(['registrationPayment', 'monthlyPayments'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($member) use ($fee) {
                // Registration status
                $member->reg_status = optional($member->registrationPayment)->status ?? 'pending';

                // Monthly status: compare total paid vs total due
                $totalPaid   = (float) $member->monthlyPayments->sum('paid_amount');
                $monthsCount = count(MonthlyPayment::allMonthsForMember($member));
                $totalDue    = $monthsCount * $fee;

                if ($totalPaid <= 0) {
                    $member->monthly_status = 'unpaid';
                } elseif ($totalPaid >= $totalDue + 0.001) {
                    $member->monthly_status = 'overpaid';
                } elseif ($totalPaid >= $totalDue - 0.001) {
                    $member->monthly_status = 'paid';
                } else {
                    $member->monthly_status = 'partial';
                }

                $member->monthly_paid    = $totalPaid;
                $member->monthly_balance = max(0, $totalDue - $totalPaid);

                return $member;
            });

        // ── Sidebar badge counts ──────────────────────────────────────
        view()->share('pendingReg',     $pendingRegistration);
        view()->share('pendingMonthly', $countPartial + $countNeverPaid);

        return view('dashboard', [
            'stats' => [
                'total_members'         => $totalMembers,
                'pending_registration'  => $pendingRegistration,
                'monthly_outstanding'   => $monthlyOutstanding,
                'monthly_income'        => $monthlyIncome,
                'count_paid'            => $countPaid,
                'count_partial'         => $countPartial,
                'count_overpaid'        => $countOverpaid,
                'count_never_paid'      => $countNeverPaid,
                'total_donations'       => $totalDonations,
                'registration_income'   => $registrationIncome,
                'total_income'          => $totalIncome,
            ],
            'recentMembers'         => $recentMembers,
            'recentMonthlyPayments' => $recentMonthlyPayments,
        ]);
    }
}
