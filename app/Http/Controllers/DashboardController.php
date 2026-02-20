<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\RegistrationPayment;
use App\Models\MonthlyPayment;
use App\Models\Donation;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with aggregated statistics.
     */
    public function index()
    {
        // ── Member Count ─────────────────────────────────────────────────
        $totalMembers = Member::count();

        // ── Registration Payments ────────────────────────────────────────
        // Count of members whose registration fee is NOT yet paid
        $pendingRegistration = Member::whereDoesntHave('registrationPayment', fn ($q) =>
            $q->where('status', 'paid')
        )->count();

        // Sum of all confirmed registration fee income
        $registrationIncome = RegistrationPayment::where('status', 'paid')->sum('paid_amount');

        // ── Monthly Fees ─────────────────────────────────────────────────
        // Total outstanding amount for the current month
        $monthlyFeesPending = MonthlyPayment::whereIn('status', ['unpaid', 'partial'])
            ->where('month', now()->month)
            ->where('year',  now()->year)
            ->sum('balance_amount');

        // Sum of all collected monthly fees
        $monthlyIncome = MonthlyPayment::where('status', 'paid')->sum('total_amount');

        // ── Donations ────────────────────────────────────────────────────
        $totalDonations = Donation::where('status', 'received')->sum('amount');

        // ── Total Income ─────────────────────────────────────────────────
        $totalIncome = $registrationIncome + $monthlyIncome + $totalDonations;

        // ── Recent Members (latest 6) ─────────────────────────────────────
        // We load reg & monthly payment status via eager loading
        $recentMembers = Member::with(['registrationPayment', 'currentMonthPayment'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($member) {
                $member->reg_status     = optional($member->registrationPayment)->status ?? 'pending';
                $member->monthly_status = optional($member->currentMonthPayment)->status  ?? 'pending';
                return $member;
            });
        $totalDonations   = Donation::received()->sum('amount');
        $donationsThisMonth = Donation::received()
            ->whereYear('donation_date',  now()->year)
            ->whereMonth('donation_date', now()->month)
            ->sum('amount');

        // ── Pass badge counts to layout (sidebar notification pills) ─────
        view()->share('pendingReg',     $pendingRegistration);
        view()->share('pendingMonthly', MonthlyPayment::where('status', 'pending')->count());

        return view('dashboard', [
            'stats' => [
                'total_members'         => $totalMembers,
                'pending_registration'  => $pendingRegistration,
                'monthly_fees_pending'  => $monthlyFeesPending,
                'total_donations'       => $totalDonations,
                'registration_income'   => $registrationIncome,
                'monthly_income'        => $monthlyIncome,
                'total_income'          => $totalIncome,
                'total_donations'        => $totalDonations,
                'donations_this_month'   => $donationsThisMonth,
                'donation_count'         => Donation::received()->count(),
            ],
            'recentMembers' => $recentMembers,
        ]);
    }
}
