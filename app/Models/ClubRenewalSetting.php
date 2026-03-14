<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubRenewalSetting extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'season_label',
        'title',
        'description',
        'fee_amount',
        'fee_currency',
        'renewal_deadline',
        'payment_details',
        'is_open',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'renewal_deadline' => 'date',
            'fee_amount' => 'decimal:2',
            'is_open' => 'boolean',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}