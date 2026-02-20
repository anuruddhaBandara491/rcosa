<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'reason',
        'amount',
        'donation_date',
        'receipt_number',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'donation_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // ── Computed Attributes ────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'received' => 'Received',
            'pending'  => 'Pending',
            default    => ucfirst($this->status),
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rs ' . number_format($this->amount, 2);
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeReceived($q)  { return $q->where('status', 'received'); }
    public function scopePending($q)   { return $q->where('status', 'pending');  }

    public function scopeDateRange($q, ?string $from, ?string $to)
    {
        if ($from) $q->where('donation_date', '>=', $from);
        if ($to)   $q->where('donation_date', '<=', $to);
        return $q;
    }

    public function scopeSearch($q, string $term)
    {
        return $q->where(function ($inner) use ($term) {
            $inner->where('reason', 'like', "%{$term}%")
                  ->orWhereHas('member', fn($mq) =>
                      $mq->where('name_with_initials', 'like', "%{$term}%")
                         ->orWhere('nic_number', 'like', "%{$term}%")
                  );
        });
    }

    // ── Static Helpers ─────────────────────────────────────────
    public static function totalReceived(): float
    {
        return (float) static::received()->sum('amount');
    }

    public static function totalThisMonth(): float
    {
        return (float) static::received()
            ->whereYear('donation_date',  now()->year)
            ->whereMonth('donation_date', now()->month)
            ->sum('amount');
    }

    public static function totalThisYear(): float
    {
        return (float) static::received()
            ->whereYear('donation_date', now()->year)
            ->sum('amount');
    }
}
