<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DonationController extends Controller
{
    // ── AJAX: Member search for Select2 ───────────────────────
    // GET /donations/search-member?q=...
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

        // Select2 expects { results: [{id, text}] }
        return response()->json([
            'results' => $members->map(fn($m) => [
                'id'         => $m->id,
                'text'       => $m->name_with_initials . ' — ' . $m->nic_number,
                'name'       => $m->name_with_initials,
                'nic'        => $m->nic_number,
                'phone'      => $m->phone_number,
                'occupation' => $m->occupation,
                'city'       => $m->current_city,
                'initials'   => strtoupper(substr($m->name_with_initials, 0, 1)),
            ]),
        ]);
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Donation::with('member');

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Date range filter
        if ($from = $request->input('date_from')) {
            $query->where('donation_date', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->where('donation_date', '<=', $to);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $donations = $query->orderBy('donation_date', 'desc')
                           ->paginate(15)
                           ->withQueryString();

        // Stats for header cards
        $stats = [
            'total_all_time'  => Donation::received()->sum('amount'),
            'total_this_year' => Donation::totalThisYear(),
            'total_this_month'=> Donation::totalThisMonth(),
            'total_count'     => Donation::received()->count(),
        ];

        // Stats for the filtered result (date range)
        $filteredTotal = 0;
        if ($from || $to) {
            $filteredTotal = Donation::received()
                ->dateRange($from, $to)
                ->sum('amount');
        }

        return view('donations.index', compact('donations', 'stats', 'filteredTotal'));
    }

    // ── CREATE ────────────────────────────────────────────────
    public function create()
    {
        return view('donations.create');
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => ['required', 'exists:members,id'],
            'reason'         => ['required', 'string', 'max:500'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'donation_date'  => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'status'         => ['required', 'in:received,pending'],
        ], [
            'member_id.required'     => 'Please select a member.',
            'member_id.exists'       => 'Selected member does not exist.',
            'reason.required'        => 'Reason for donation is required.',
            'amount.required'        => 'Donation amount is required.',
            'amount.min'             => 'Amount must be at least Rs 1.',
            'donation_date.required' => 'Donation date is required.',
            'donation_date.before_or_equal' => 'Donation date cannot be in the future.',
        ]);

        $donation = Donation::create($validated);

        return redirect()
            ->route('donations.show', $donation)
            ->with('success', "Donation of {$donation->formatted_amount} from {$donation->member->name_with_initials} recorded successfully.");
    }
    public function memberTotal(Member $member): JsonResponse
    {
        $total = Donation::where('member_id', $member->id)
                        ->received()
                        ->sum('amount');

        return response()->json([
            'total'     => (float) $total,
            'formatted' => 'Rs ' . number_format($total, 2),
        ]);
    }
    // ── SHOW ──────────────────────────────────────────────────
    public function show(Donation $donation)
    {
        $donation->load('member');

        // Other donations by same member
        $otherDonations = Donation::where('member_id', $donation->member_id)
            ->where('id', '!=', $donation->id)
            ->orderBy('donation_date', 'desc')
            ->limit(5)
            ->get();

        $memberTotal = Donation::where('member_id', $donation->member_id)
            ->received()
            ->sum('amount');

        return view('donations.show', compact('donation', 'otherDonations', 'memberTotal'));
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(Donation $donation)
    {
        $donation->load('member');
        return view('donations.edit', compact('donation'));
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'member_id'      => ['required', 'exists:members,id'],
            'reason'         => ['required', 'string', 'max:500'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'donation_date'  => ['required', 'date', 'before_or_equal:today'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'status'         => ['required', 'in:received,pending'],
        ]);

        $donation->update($validated);

        return redirect()
            ->route('donations.show', $donation)
            ->with('success', 'Donation record updated successfully.');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Donation $donation)
    {
        $info = $donation->formatted_amount . ' from ' . ($donation->member->name_with_initials ?? 'member');
        $donation->delete();

        return redirect()
            ->route('donations.index')
            ->with('success', "Donation ({$info}) deleted successfully.");
    }
}
