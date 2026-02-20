<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MonthlyPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MonthlyPaymentController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // AJAX: Member Search (used by the live search dropdown)
    // GET /monthly-payments/search-member?q=...
    // ─────────────────────────────────────────────────────────
    public function searchMember(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $members = Member::where('name_with_initials', 'like', "%{$term}%")
            ->orWhere('nic_number',   'like', "%{$term}%")
            ->orWhere('phone_number', 'like', "%{$term}%")
            ->orderBy('name_with_initials')
            ->limit(10)
            ->get(['id', 'name_with_initials', 'nic_number', 'phone_number', 'occupation', 'current_city']);

        return response()->json($members->map(fn($m) => [
            'id'          => $m->id,
            'name'        => $m->name_with_initials,
            'nic'         => $m->nic_number,
            'phone'       => $m->phone_number,
            'occupation'  => $m->occupation,
            'city'        => $m->current_city,
            'initials'    => strtoupper(substr($m->name_with_initials, 0, 1)),
        ]));
    }

    // ─────────────────────────────────────────────────────────
    // AJAX: Member Payment Summary
    // GET /monthly-payments/member-summary/{member}
    // ─────────────────────────────────────────────────────────
    public function memberSummary(Member $member): JsonResponse
    {
        $fee          = MonthlyPayment::monthlyFee();
        $unpaidMonths = MonthlyPayment::unpaidMonthsForMember($member);

        $totalBalance = array_sum(array_column($unpaidMonths, 'balance'));
        $totalUnpaid  = count(array_filter($unpaidMonths, fn($m) => $m['status'] === 'unpaid'));
        $totalPartial = count(array_filter($unpaidMonths, fn($m) => $m['status'] === 'partial'));

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
            'fee'           => $fee,
            'unpaid_months' => $unpaidMonths,
            'summary' => [
                'total_balance'  => $totalBalance,
                'count_unpaid'   => $totalUnpaid,
                'count_partial'  => $totalPartial,
                'total_months'   => count($unpaidMonths),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MonthlyPayment::with('member');

        if ($search = $request->input('search')) {
            $query->search($search);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($year = $request->input('year')) {
            $query->where('year', $year);
        }
        if ($month = $request->input('month')) {
            $query->where('month', $month);
        }

        $payments = $query->orderBy('year', 'desc')
                          ->orderBy('month', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        $stats = [
            'total_collected' => MonthlyPayment::sum('paid_amount'),
            'total_balance'   => MonthlyPayment::where('status', '!=', 'paid')->sum('balance_amount'),
            'count_paid'      => MonthlyPayment::fullyPaid()->count(),
            'count_partial'   => MonthlyPayment::partial()->count(),
            'count_unpaid'    => MonthlyPayment::unpaid()->count(),
        ];

        $years = MonthlyPayment::selectRaw('DISTINCT year')->orderBy('year', 'desc')->pluck('year');

        return view('monthly-payments.index', compact('payments', 'stats', 'years'));
    }

    // ─────────────────────────────────────────────────────────
    // CREATE (main payment form)
    // ─────────────────────────────────────────────────────────
    public function create()
    {
        $fee = MonthlyPayment::monthlyFee();
        return view('monthly-payments.create', compact('fee'));
    }

    // ─────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $fee = MonthlyPayment::monthlyFee();

        $validated = $request->validate([
            'member_id'      => ['required', 'exists:members,id'],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
            // payments is an array: [{month, year, pay_amount}]
            'payments'                => ['required', 'array', 'min:1'],
            'payments.*.month'        => ['required', 'integer', 'min:1', 'max:12'],
            'payments.*.year'         => ['required', 'integer', 'min:2000'],
            'payments.*.pay_amount'   => ['required', 'numeric', 'min:0'],
        ], [
            'member_id.required'    => 'Please search and select a member.',
            'payments.required'     => 'Please enter at least one payment.',
            'payments.min'          => 'At least one month payment is required.',
        ]);

        $memberId      = $validated['member_id'];
        $paymentDate   = $validated['payment_date'];
        $receiptNumber = $validated['receipt_number'] ?? null;
        $notes         = $validated['notes'] ?? null;
        $recorded      = 0;

        foreach ($validated['payments'] as $entry) {
            $payAmount = (float) $entry['pay_amount'];
            if ($payAmount <= 0) continue; // skip zero entries

            $month = (int) $entry['month'];
            $year  = (int) $entry['year'];

            // Find or create the record for this month/year
            $record = MonthlyPayment::firstOrNew([
                'member_id' => $memberId,
                'month'     => $month,
                'year'      => $year,
            ]);

            $prevPaid   = $record->exists ? (float) $record->paid_amount : 0;
            $newPaid    = min($fee, $prevPaid + $payAmount);
            $newBalance = max(0, $fee - $newPaid);
            $newStatus  = MonthlyPayment::computeStatus($newPaid, $fee);

            $record->fill([
                'total_amount'   => $fee,
                'paid_amount'    => $newPaid,
                'balance_amount' => $newBalance,
                'status'         => $newStatus,
                'payment_date'   => $paymentDate,
                'receipt_number' => $receiptNumber,
                'notes'          => $notes,
            ])->save();

            $recorded++;
        }

        if ($recorded === 0) {
            return back()->withErrors(['payments' => 'No valid payment amounts entered.'])->withInput();
        }

        return redirect()
            ->route('monthly-payments.index')
            ->with('success', "{$recorded} monthly payment(s) recorded successfully.");
    }

    // ─────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────
    public function show(MonthlyPayment $monthlyPayment)
    {
        $monthlyPayment->load('member');
        return view('monthly-payments.show', ['payment' => $monthlyPayment]);
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────
    public function edit(MonthlyPayment $monthlyPayment)
    {
        $monthlyPayment->load('member');
        $fee = MonthlyPayment::monthlyFee();
        return view('monthly-payments.edit', ['payment' => $monthlyPayment, 'fee' => $fee]);
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, MonthlyPayment $monthlyPayment)
    {
        $fee = MonthlyPayment::monthlyFee();

        $validated = $request->validate([
            'paid_amount'    => ['required', 'numeric', 'min:0', 'max:' . $fee],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $paid    = (float) $validated['paid_amount'];
        $balance = max(0, $fee - $paid);
        $status  = MonthlyPayment::computeStatus($paid, $fee);

        $monthlyPayment->update([
            'total_amount'   => $fee,
            'paid_amount'    => $paid,
            'balance_amount' => $balance,
            'status'         => $status,
            'payment_date'   => $validated['payment_date'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('monthly-payments.show', $monthlyPayment)
            ->with('success', 'Payment record updated successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────
    public function destroy(MonthlyPayment $monthlyPayment)
    {
        $label = $monthlyPayment->month_label . ' — ' . ($monthlyPayment->member->name_with_initials ?? '');
        $monthlyPayment->delete();

        return redirect()
            ->route('monthly-payments.index')
            ->with('success', "Payment record \"{$label}\" deleted.");
    }
}
