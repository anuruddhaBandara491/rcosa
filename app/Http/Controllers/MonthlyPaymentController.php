<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MonthlyPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MonthlyPaymentController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // AJAX: Member Search
    // GET /monthly-payments/search-member?q=...
    // ─────────────────────────────────────────────────────────────
    public function searchMember(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (strlen($term) < 3) {
            return response()->json([]);
        }

        $members = Member::where('name_with_initials', 'like', "%{$term}%")
            ->orWhere('nic_number',   'like', "%{$term}%")
            ->orWhere('phone_number', 'like', "%{$term}%")
            ->orderBy('name_with_initials')
            ->limit(10)
            ->get(['id', 'name_with_initials', 'nic_number', 'phone_number', 'occupation', 'current_city']);

        return response()->json($members->map(fn($m) => [
            'id'         => $m->id,
            'name'       => $m->name_with_initials,
            'nic'        => $m->nic_number,
            'phone'      => $m->phone_number,
            'occupation' => $m->occupation,
            'city'       => $m->current_city,
            'initials'   => strtoupper(substr($m->name_with_initials, 0, 1)),
        ]));
    }

    // ─────────────────────────────────────────────────────────────
    // AJAX: Member Summary for the payment form
    // GET /monthly-payments/member-summary/{member}
    // ─────────────────────────────────────────────────────────────
    public function memberSummary(Member $member): JsonResponse
    {
        $data = MonthlyPayment::memberSummaryForForm($member);

        return response()->json([
            'member' => [
                'id'         => $member->id,
                'name'       => $member->name_with_initials,
                'nic'        => $member->nic_number,
                'phone'      => $member->phone_number,
                'occupation' => $member->occupation,
                'city'       => $member->current_city,
                'initials'   => strtoupper(substr($member->name_with_initials, 0, 1)),
            ],
            'fee'           => MonthlyPayment::monthlyFee(),
            'unpaid_months' => $data['unpaid_months'],
            'summary'       => $data['summary'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MonthlyPayment::with('member');

        if ($search = $request->input('search')) {
            $query->search($search);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payments = $query->orderBy('payment_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        $stats = [
            'total_collected' => MonthlyPayment::sum('paid_amount'),
            'total_balance'   => MonthlyPayment::latest('id')->value('balance_amount') ?? 0,
            'count_paid'      => MonthlyPayment::fullyPaid()->count(),
            'count_partial'   => MonthlyPayment::partial()->count(),
            'count_overpaid'  => MonthlyPayment::overpaid()->count(),
        ];

        return view('monthly-payments.index', compact('payments', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────
    public function create()
    {
        $fee = MonthlyPayment::monthlyFee();
        return view('monthly-payments.create', compact('fee'));
    }

    // ─────────────────────────────────────────────────────────────
    // STORE  ← THE KEY METHOD
    //
    // One form submission = ONE row in monthly_payments.
    // We receive the total amount paid, compute cumulative paid,
    // derive status vs total due, and store months_covered JSON.
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $fee = MonthlyPayment::monthlyFee();

        $validated = $request->validate([
            'member_id'      => ['required', 'exists:members,id'],
            'paid_amount'    => ['required', 'numeric', 'min:0.01'],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ], [
            'member_id.required' => 'Please search and select a member.',
            'paid_amount.min'    => 'Payment amount must be greater than zero.',
        ]);

        $member     = Member::findOrFail($validated['member_id']);
        $paidNow    = (float) $validated['paid_amount'];

        // ── 1. How much has this member paid in total BEFORE this payment
        $prevPaid   = MonthlyPayment::totalPaidByMember($member->id);

        // ── 2. How many months does this member owe up to today
        $allMonths  = MonthlyPayment::allMonthsForMember($member);
        $totalDue   = count($allMonths) * $fee;

        // ── 3. Running cumulative after THIS payment
        $cumulative = $prevPaid + $paidNow;

        // ── 4. Balance (can be 0 if fully paid, negative if overpaid)
        $balance    = $totalDue - $cumulative;

        // ── 5. Status based on cumulative vs total due
        $status     = MonthlyPayment::computeStatus($cumulative, $totalDue);

        // ── 6. Work out which months this payment covers (for months_covered JSON)
        $monthsCovered = MonthlyPayment::resolveMontsCovered($member, $paidNow);

        // ── 7. Insert ONE row
        $payment = MonthlyPayment::create([
            'member_id'      => $member->id,
            'paid_amount'    => $paidNow,
            'total_due'      => $totalDue,
            'cumulative_paid'=> $cumulative,
            'balance_amount' => $balance,           // negative = overpaid
            'status'         => $status,
            'months_covered' => $monthsCovered,
            'payment_date'   => $validated['payment_date'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        $statusLabel = match($status) {
            'paid'     => 'Fully Paid ✓',
            'overpaid' => 'Overpaid — credit of Rs ' . number_format(abs($balance), 2) . ' on account',
            default    => 'Partial — Rs ' . number_format(abs($balance), 2) . ' still outstanding',
        };

        return redirect()
            ->route('monthly-payments.index')
            ->with('success', "Payment of Rs " . number_format($paidNow, 2) . " recorded for {$member->name_with_initials}. Status: {$statusLabel}.");
    }

    // ─────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────
    public function show(MonthlyPayment $monthlyPayment)
    {
        $monthlyPayment->load('member');
        return view('monthly-payments.show', ['payment' => $monthlyPayment]);
    }

    // ─────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────
    public function edit(MonthlyPayment $monthlyPayment)
    {
        $monthlyPayment->load('member');
        $fee = MonthlyPayment::monthlyFee();

        // Sum of all OTHER transactions for this member (excluding the one being edited)
        // Needed by the live calc panel in the edit view
        $otherPaid = (float) MonthlyPayment::where('member_id', $monthlyPayment->member_id)
                        ->where('id', '!=', $monthlyPayment->id)
                        ->sum('paid_amount');

        return view('monthly-payments.edit', [
            'payment'    => $monthlyPayment,
            'fee'        => $fee,
            'otherPaid'  => $otherPaid,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // UPDATE
    // When editing a transaction, we recompute the cumulative by
    // summing all OTHER transactions for this member + new amount.
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, MonthlyPayment $monthlyPayment)
    {
        $fee = MonthlyPayment::monthlyFee();

        $validated = $request->validate([
            'paid_amount'    => ['required', 'numeric', 'min:0.01'],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $member  = $monthlyPayment->member;
        $paidNow = (float) $validated['paid_amount'];

        // Sum all OTHER payments for this member (excluding the one being edited)
        $otherPaid  = MonthlyPayment::where('member_id', $member->id)
                        ->where('id', '!=', $monthlyPayment->id)
                        ->sum('paid_amount');

        $allMonths  = MonthlyPayment::allMonthsForMember($member);
        $totalDue   = count($allMonths) * $fee;
        $cumulative = (float) $otherPaid + $paidNow;
        $balance    = $totalDue - $cumulative;
        $status     = MonthlyPayment::computeStatus($cumulative, $totalDue);

        $monthlyPayment->update([
            'paid_amount'     => $paidNow,
            'total_due'       => $totalDue,
            'cumulative_paid' => $cumulative,
            'balance_amount'  => $balance,
            'status'          => $status,
            'payment_date'    => $validated['payment_date'],
            'receipt_number'  => $validated['receipt_number'] ?? null,
            'notes'           => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('monthly-payments.show', $monthlyPayment)
            ->with('success', 'Payment record updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy(MonthlyPayment $monthlyPayment)
    {
        $member = $monthlyPayment->member;
        $amt    = number_format($monthlyPayment->paid_amount, 2);
        $monthlyPayment->delete();

        return redirect()
            ->route('monthly-payments.index')
            ->with('success', "Payment of Rs {$amt} for {$member->name_with_initials} deleted.");
    }
}
