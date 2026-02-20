<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\RegistrationPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MemberReportController extends Controller
{
    // ──────────────────────────────────────────────────────
    // Main page – shows search box; if member_id is present
    // loads the full member profile with all financials.
    // GET /member-reports
    // GET /member-reports?member_id=5
    // ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $member     = null;
        $financials = null;

        if ($memberId = $request->input('member_id')) {
            $member = Member::with([
                'registrationPayment',
                'monthlyPayments',
                'donations',
            ])->findOrFail($memberId);

            $reg    = $member->registrationPayment;
            $regFee = (float) RegistrationPayment::registrationFee();

            $financials = [
                // Registration
                'reg_fee'     => $regFee,
                'reg_paid'    => $reg ? (float) $reg->paid_amount    : 0,
                'reg_balance' => $reg ? (float) $reg->balance_amount : $regFee,
                'reg_status'  => $reg ? $reg->status                 : 'unpaid',
                'reg_pct'     => $reg && $regFee > 0
                                     ? min(100, round(($reg->paid_amount / $regFee) * 100))
                                     : 0,

                // Monthly payments
                'monthly_paid'       => $member->totalMonthlyPaid(),
                'monthly_balance'    => $member->totalMonthlyBalance(),
                'monthly_count'      => $member->monthlyPayments->count(),
                'monthly_paid_count' => $member->monthlyPayments->where('status', 'paid')->count(),

                // Donations
                'donation_total' => $member->totalDonations(),
                'donation_count' => $member->donations->where('status', 'received')->count(),

                // Grand total
                'total_contribution' =>
                    ($reg ? (float) $reg->paid_amount : 0) +
                    $member->totalMonthlyPaid() +
                    $member->totalDonations(),
            ];
        }

        // 5 recent members for quick-access chips
        $recentMembers = Member::latest()->limit(5)->get(['id', 'name_with_initials']);

        return view('member-reports.index', compact('member', 'financials', 'recentMembers'));
    }

    // ──────────────────────────────────────────────────────
    // AJAX live search
    // GET /member-reports/search?q=...
    // ──────────────────────────────────────────────────────
    public function search(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (strlen($term) < 1) {
            return response()->json([]);
        }

        $members = Member::search($term)
            ->orderBy('name_with_initials')
            ->limit(12)
            ->get(['id', 'name_with_initials', 'nic_number', 'phone_number', 'occupation', 'current_city', 'membership_number']);

        return response()->json($members->map(fn ($m) => [
            'id'           => $m->id,
            'name'         => $m->name_with_initials,
            'nic'          => $m->nic_number,
            'phone'        => $m->phone_number,
            'occupation'   => $m->occupation,
            'city'         => $m->current_city,
            'membership_no'=> $m->membership_number,
            'initials'     => strtoupper(substr($m->name_with_initials, 0, 1)),
        ]));
    }
}
