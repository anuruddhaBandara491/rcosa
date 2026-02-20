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
        'month',
        'year',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'payment_date',
        'receipt_number',
        'notes',
    ];

    protected $casts = [
        'total_amount'   => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'payment_date'   => 'date',
        'month'          => 'integer',
        'year'           => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // ── Computed Attributes ────────────────────────────────────
    public function getMonthNameAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F');
    }

    public function getMonthLabelAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'Fully Paid',
            'partial' => 'Partial',
            'unpaid'  => 'Unpaid',
            default   => ucfirst($this->status),
        };
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_amount <= 0) return 0;
        return (int) min(100, round(($this->paid_amount / $this->total_amount) * 100));
    }

    // ── Static Helpers ─────────────────────────────────────────
    public static function monthlyFee(): float
    {
        return (float) env('MONTHLY_FEE', 1000);
    }

    public static function computeStatus(float $paid, float $total): string
    {
        if ($paid <= 0)      return 'unpaid';
        if ($paid >= $total) return 'paid';
        return 'partial';
    }

    /**
     * Get unpaid/partial months for a member from their start year to now.
     */
    public static function unpaidMonthsForMember(Member $member, int $startYear = null): array
    {
        $fee   = self::monthlyFee();
        $start = Carbon::createFromDate(
            $startYear ?? ($member->school_register_year ?? now()->year),
            1, 1
        );
        $now = now()->startOfMonth();

        $existing = self::where('member_id', $member->id)
            ->get()
            ->keyBy(fn($r) => "{$r->year}-{$r->month}");

        $months = [];
        $cursor = $start->copy();

        while ($cursor->lte($now)) {
            $key    = "{$cursor->year}-{$cursor->month}";
            $record = $existing->get($key);

            $months[] = [
                'year'    => $cursor->year,
                'month'   => $cursor->month,
                'label'   => $cursor->format('F Y'),
                'fee'     => $fee,
                'paid'    => $record ? (float) $record->paid_amount    : 0,
                'balance' => $record ? (float) $record->balance_amount : $fee,
                'status'  => $record ? $record->status                 : 'unpaid',
                'id'      => $record?->id,
            ];

            $cursor->addMonth();
        }

        return array_values(array_filter($months, fn($m) => $m['status'] !== 'paid'));
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeUnpaid($q)    { return $q->where('status', 'unpaid');  }
    public function scopePartial($q)   { return $q->where('status', 'partial'); }
    public function scopeFullyPaid($q) { return $q->where('status', 'paid');    }

    public function scopeForMonth($q, int $month, int $year)
    {
        return $q->where('month', $month)->where('year', $year);
    }

    public function scopeSearch($q, string $term)
    {
        return $q->whereHas('member', fn($mq) =>
            $mq->where('name_with_initials', 'like', "%{$term}%")
               ->orWhere('nic_number',        'like', "%{$term}%")
               ->orWhere('phone_number',      'like', "%{$term}%")
        );
    }
}
