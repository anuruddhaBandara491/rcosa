<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrationPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
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
    ];

    // ── Relationships ───────────────────────────────────────────
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // ── Computed attributes ─────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'Fully Paid',
            'partial' => 'Partial',
            'unpaid'  => 'Unpaid',
            default   => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'success',
            'partial' => 'warning',
            'unpaid'  => 'danger',
            default   => 'secondary',
        };
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_amount <= 0) return 0;
        return (int) min(100, round(($this->paid_amount / $this->total_amount) * 100));
    }

    // ── Static helpers ──────────────────────────────────────────

    /**
     * Resolve the registration fee from .env (REGISTRATION_FEE).
     */
    public static function registrationFee(): float
    {
        return (float) env('REGISTRATION_FEE', 5000);
    }

    /**
     * Compute the new status given paid vs total.
     */
    public static function computeStatus(float $paid, float $total): string
    {
        if ($paid <= 0)         return 'unpaid';
        if ($paid >= $total)    return 'paid';
        return 'partial';
    }

    // ── Scopes ──────────────────────────────────────────────────
    public function scopeUnpaid($q)   { return $q->where('status', 'unpaid');  }
    public function scopePartial($q)  { return $q->where('status', 'partial'); }
    public function scopeFullyPaid($q){ return $q->where('status', 'paid');    }
    public function scopeSearch($q, string $term)
    {
        return $q->whereHas('member', fn($mq) =>
            $mq->where('name_with_initials', 'like', "%{$term}%")
               ->orWhere('nic_number',        'like', "%{$term}%")
               ->orWhere('phone_number',      'like', "%{$term}%")
        );
    }
}
