<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubMembership extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'role',
        'is_club_admin',
        'is_paid',
        'joined_on',
        'last_paid_on',
        'ends_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_club_admin' => 'boolean',
            'is_paid' => 'boolean',
            'joined_on' => 'date',
            'last_paid_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function user(): BelongsTo
    {
        return $this->member();
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(ClubRenewalRequest::class);
    }
}