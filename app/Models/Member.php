<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'membership_number',
        'name_with_initials',
        'school_register_year',
        'married',
        'phone_number',
        'nic_number',
        'birthday',
        'address',
        'email',
        'occupation',
        'current_city',
        'gender',
        'children_info',
        'siblings_info',
        'district',
        'election_division',
        'grama_niladhari_division',
        'admission_number',
        'date_joined_school',
    ];

    protected $casts = [
        'married'            => 'boolean',
        'birthday'           => 'date',
        'date_joined_school' => 'date',
        'children_info'      => 'array',
    ];

    // ── Computed ───────────────────────────────────────────────
    public function getAgeAttribute(): int
    {
        return $this->birthday->age;
    }

    public function getMarriedLabelAttribute(): string
    {
        return $this->married ? 'Married' : 'Unmarried';
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name_with_initials', 'like', "%{$term}%")
              ->orWhere('nic_number',        'like', "%{$term}%")
              ->orWhere('phone_number',      'like', "%{$term}%");
        });
    }

    // ── Relationships (extend later) ───────────────────────────
    public function registrationPayment()
    {
        return $this->hasOne(RegistrationPayment::class);
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    public function currentMonthPayment()
    {
        return $this->hasOne(MonthlyPayment::class)
                    ->whereYear('balance_amount',  now()->year)
                    ->whereMonth('balance_amount', now()->month);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
    public function totalMonthlyPaid(): float
    {
        return (float) $this->monthlyPayments()->sum('paid_amount');
    }

    public function totalMonthlyBalance(): float
    {
        return (float) $this->monthlyPayments()
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->sum('balance_amount');
    }
    public function registrationBalance(): float
    {
        return (float) optional($this->registrationPayment)->balance_amount ?? 0;
    }

    public function totalDonations(): float
    {
        return (float) $this->donations()->received()->sum('amount');
    }

}
