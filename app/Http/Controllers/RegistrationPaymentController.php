<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\RegistrationPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistrationPaymentController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = RegistrationPayment::with('member');

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // Summary stats for the header cards
        $stats = [
            'total_collected' => RegistrationPayment::sum('paid_amount'),
            'total_balance'   => RegistrationPayment::sum('balance_amount'),
            'count_paid'      => RegistrationPayment::fullyPaid()->count(),
            'count_partial'   => RegistrationPayment::partial()->count(),
            'count_unpaid'    => RegistrationPayment::unpaid()->count(),
        ];

        $fee = RegistrationPayment::registrationFee();

        return view('registration-payments.index', compact('payments', 'stats', 'fee'));
    }
    public function searchMember(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (mb_strlen($term) < 3) {
            return response()->json(['results' => []]);
        }

        $members = Member::with('registrationPayment')
            ->where(function ($q) use ($term) {
                $q->where('name_with_initials', 'like', "%{$term}%")
                ->orWhere('nic_number',        'like', "%{$term}%");
            })
            ->orderBy('name_with_initials')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $members->map(fn ($m) => [
                'id'         => $m->id,
                'text'       => $m->name_with_initials . ' — ' . $m->nic_number,
                'name'       => $m->name_with_initials,
                'nic'        => $m->nic_number,
                'phone'      => $m->phone_number,
                'initials'   => strtoupper(substr($m->name_with_initials, 0, 1)),
                'reg_status' => $m->registrationPayment->status    ?? 'unpaid',
                'reg_paid'   => (float) ($m->registrationPayment->paid_amount ?? 0),
            ]),
        ]);
    }

    // ── CREATE ─────────────────────────────────────────────────
    public function create(Request $request)
    {
        $fee = RegistrationPayment::registrationFee();

        // Pre-select member if passed via query string (?member_id=X)
        $selectedMember = null;
        if ($memberId = $request->query('member_id')) {
            $selectedMember = Member::find($memberId);
        }

        return view('registration-payments.create', compact('fee', 'selectedMember'));
    }

    // ── STORE ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $fee = RegistrationPayment::registrationFee();

        $validated = $request->validate([
            'member_id'      => ['required', 'exists:members,id'],
            'paid_amount'    => ['required', 'numeric', 'min:1', 'max:' . $fee],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'discount_amount'=> ['nullable', 'numeric', 'min:0', 'max:' . $fee],
        ], [
            'member_id.required'   => 'Please select a member.',
            'member_id.exists'     => 'Selected member does not exist.',
            'paid_amount.required' => 'Paid amount is required.',
            'paid_amount.min'      => 'Paid amount must be at least Rs 1.',
            'paid_amount.max'      => "Paid amount cannot exceed the total fee of Rs {$fee}.",
            'payment_date.required'=> 'Payment date is required.',
        ]);

        $memberId   = $validated['member_id'];
        $paidNow    = (float) $validated['paid_amount'];

        // Check if a payment record already exists for this member
        $existing = RegistrationPayment::where('member_id', $memberId)->first();

        if ($existing) {
            // Add to existing payment (top-up / second installment)
            $newPaid    = min($existing->paid_amount + $paidNow, $fee);
            $newBalance = $fee - $newPaid;
            $newStatus  = RegistrationPayment::computeStatus($newPaid, $fee);

            $existing->update([
                'paid_amount'    => $newPaid,
                'balance_amount' => $newBalance,
                'status'         => $newStatus,
                'payment_date'   => $validated['payment_date'],
                'receipt_number' => $validated['receipt_number'] ?? $existing->receipt_number,
                'notes'          => $validated['notes'] ?? $existing->notes,
            ]);

            $payment = $existing;

        } else {
            // Brand new payment record
            $balance = $fee - $paidNow;
            $status  = RegistrationPayment::computeStatus($paidNow, $fee);

            $payment = RegistrationPayment::create([
                'member_id'      => $memberId,
                'total_amount'   => $fee,
                'paid_amount'    => $paidNow,
                'balance_amount' => $balance,
                'status'         => $status,
                'payment_date'   => $validated['payment_date'],
                'receipt_number' => $validated['receipt_number'] ?? null,
                'notes'          => $validated['notes'] ?? null,
            ]);
        }

        return redirect()
            ->route('registration-payments.show', $payment)
            ->with('success', "Payment recorded for {$payment->member->name_with_initials}. Balance: Rs " . number_format($payment->balance_amount, 2));
    }

    // ── SHOW ───────────────────────────────────────────────────
    public function show(RegistrationPayment $registrationPayment)
    {
        $registrationPayment->load('member');
        return view('registration-payments.show', [
            'payment' => $registrationPayment,
        ]);
    }

    // ── EDIT ───────────────────────────────────────────────────
    public function edit(RegistrationPayment $registrationPayment)
    {
        $registrationPayment->load('member');
        $fee = RegistrationPayment::registrationFee();

        return view('registration-payments.edit', [
            'payment' => $registrationPayment,
            'fee'     => $fee,
        ]);
    }

    // ── UPDATE ─────────────────────────────────────────────────
    public function update(Request $request, RegistrationPayment $registrationPayment)
    {
        $fee = RegistrationPayment::registrationFee();

        $validated = $request->validate([
            'paid_amount'    => ['required', 'numeric', 'min:0', 'max:' . $fee],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $paidAmount    = (float) $validated['paid_amount'];
        $balanceAmount = $fee - $paidAmount;
        $status        = RegistrationPayment::computeStatus($paidAmount, $fee);

        $registrationPayment->update([
            'total_amount'   => $fee,
            'paid_amount'    => $paidAmount,
            'balance_amount' => $balanceAmount,
            'status'         => $status,
            'payment_date'   => $validated['payment_date'],
            'receipt_number' => $validated['receipt_number'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('registration-payments.show', $registrationPayment)
            ->with('success', 'Payment record updated successfully.');
    }

    // ── DESTROY ────────────────────────────────────────────────
    public function destroy(RegistrationPayment $registrationPayment)
    {
        $name = $registrationPayment->member->name_with_initials ?? 'Member';
        $registrationPayment->delete();

        return redirect()
            ->route('registration-payments.index')
            ->with('success', "Payment record for \"{$name}\" deleted.");
    }
}
