<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MonthlyPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'paid_amount',
        'total_due',
        'cumulative_paid',
        'balance_amount',
        'status',
        'months_covered',
        'payment_date',
        'receipt_number',
        'notes',
    ];

    protected $casts = [
        'paid_amount'     => 'decimal:2',
        'total_due'       => 'decimal:2',
        'cumulative_paid' => 'decimal:2',
        'balance_amount'  => 'decimal:2',
        'months_covered'  => 'array',
        'payment_date'    => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // ── Computed Attributes ────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'     => 'Fully Paid',
            'partial'  => 'Partial',
            'overpaid' => 'Overpaid',
            default    => ucfirst($this->status),
        };
    }

    public function getProgressPercentAttribute(): int
    {
        if ((float)$this->total_due <= 0) return 0;
        return (int) min(100, round(((float)$this->cumulative_paid / (float)$this->total_due) * 100));
    }

    public function getMonthsListAttribute(): string
    {
        if (empty($this->months_covered)) return '—';
        return collect($this->months_covered)->pluck('label')->join(', ');
    }

    // ── Static Helpers ─────────────────────────────────────────────

    public static function monthlyFee(): float
    {
        return (float) env('MONTHLY_FEE', 1000);
    }

    /**
     * Compute status based on cumulative paid vs total due.
     */
    public static function computeStatus(float $cumulativePaid, float $totalDue): string
    {
        if ($cumulativePaid >= $totalDue + 0.001) return 'overpaid';
        if ($cumulativePaid >= $totalDue - 0.001) return 'paid';
        return 'partial';
    }

    /**
     * Total amount the member has paid across ALL transactions.
     */
    public static function totalPaidByMember(int $memberId): float
    {
        return (float) self::where('member_id', $memberId)->sum('paid_amount');
    }

    /**
     * Calculate how many months a member owes from their start date to now.
     * Returns array of month info for the UI.
     */
    public static function allMonthsForMember(Member $member): array
    {
        $fee   = self::monthlyFee();
        $start = Carbon::createFromDate(
            $member->register_date ?? now()->year,
            1, 1
        )->startOfMonth();
        $now   = now()->endOfMonth();

        $months = [];
        $cursor = $start->copy();

        while ($cursor->lte($now)) {
            $months[] = [
                'year'  => $cursor->year,
                'month' => $cursor->month,
                'label' => $cursor->format('F Y'),
                'fee'   => $fee,
            ];
            $cursor->addMonth();
        }

        return $months;
    }

    /**
     * Build the data the create-form AJAX needs:
     * - all months from start to now
     * - how much has been paid vs owed overall
     * - running allocation of payments across months (oldest first)
     */
    public static function memberSummaryForForm(Member $member): array
    {
        $fee          = self::monthlyFee();
        $allMonths    = self::allMonthsForMember($member);
        $totalDue     = count($allMonths) * $fee;
        $totalPaid    = self::totalPaidByMember($member->id);
        $balance      = max(0, $totalDue - $totalPaid);
        $overpaid     = max(0, $totalPaid - $totalDue);

        // Allocate paid amount across months oldest-first for display
        $remaining = $totalPaid;
        $monthsWithStatus = array_map(function ($m) use ($fee, &$remaining) {
            $apply   = min($fee, $remaining);
            $remaining -= $apply;
            $paid    = $apply;
            $bal     = $fee - $paid;
            $status  = $paid <= 0 ? 'unpaid' : ($paid >= $fee ? 'paid' : 'partial');
            return array_merge($m, [
                'paid'    => $paid,
                'balance' => $bal,
                'status'  => $status,
            ]);
        }, $allMonths);

        // Only show months that are not fully paid (for the form)
        $unpaidMonths = array_values(array_filter(
            $monthsWithStatus,
            fn($m) => $m['status'] !== 'paid'
        ));

        $countUnpaid  = count(array_filter($unpaidMonths, fn($m) => $m['status'] === 'unpaid'));
        $countPartial = count(array_filter($unpaidMonths, fn($m) => $m['status'] === 'partial'));

        return [
            'total_due'     => $totalDue,
            'total_paid'    => $totalPaid,
            'balance'       => $balance,
            'overpaid'      => $overpaid,
            'unpaid_months' => $unpaidMonths,
            'summary' => [
                'total_balance' => $balance,
                'count_unpaid'  => $countUnpaid,
                'count_partial' => $countPartial,
                'total_months'  => count($unpaidMonths),
            ],
        ];
    }

    /**
     * Given a new payment amount, work out which months it covers
     * (oldest-first) for storing in months_covered JSON.
     */
    public static function resolveMontsCovered(Member $member, float $newPayAmount): array
    {
        $fee       = self::monthlyFee();
        $allMonths = self::allMonthsForMember($member);
        $prevPaid  = self::totalPaidByMember($member->id);

        // Allocate all previously paid first
        $cursor    = $prevPaid;
        $covered   = [];
        $newBucket = $newPayAmount;

        foreach ($allMonths as $m) {
            $alreadyApplied = min($fee, $cursor);
            $cursor        -= $alreadyApplied;
            $stillNeeded    = $fee - $alreadyApplied;

            if ($stillNeeded <= 0) continue; // already fully paid

            $apply     = min($stillNeeded, $newBucket);
            $newBucket -= $apply;

            if ($apply > 0) {
                $covered[] = [
                    'month'  => $m['month'],
                    'year'   => $m['year'],
                    'label'  => $m['label'],
                    'amount' => round($apply, 2),
                    'full'   => $apply >= $stillNeeded,
                ];
            }

            if ($newBucket <= 0) break;
        }

        // If still money left (overpayment beyond all months)
        if ($newBucket > 0.001) {
            $covered[] = [
                'month'  => null,
                'year'   => null,
                'label'  => 'Advance / Overpayment',
                'amount' => round($newBucket, 2),
                'full'   => false,
            ];
        }

        return $covered;
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopePartial($q)   { return $q->where('status', 'partial');  }
    public function scopeFullyPaid($q) { return $q->where('status', 'paid');     }
    public function scopeOverpaid($q)  { return $q->where('status', 'overpaid'); }

    public function scopeSearch($q, string $term)
    {
        return $q->whereHas('member', fn($mq) =>
            $mq->where('name_with_initials', 'like', "%{$term}%")
               ->orWhere('nic_number',        'like', "%{$term}%")
               ->orWhere('phone_number',      'like', "%{$term}%")
        );
    }
}
