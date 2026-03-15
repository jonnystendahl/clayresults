<?php

namespace App\Models;

use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\ClubRenewalRequest;
use App\Models\TrainingResult;
use Database\Factories\MemberFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'is_admin',
        'main_club_id',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'must_change_password' => 'boolean',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function trainingResults(): HasMany
    {
        return $this->hasMany(TrainingResult::class, 'member_id');
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(ClubRenewalRequest::class, 'member_id');
    }

    public function mainClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'main_club_id');
    }

    public function clubMemberships(): HasMany
    {
        return $this->hasMany(ClubMembership::class, 'member_id');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_memberships', 'member_id', 'club_id')
            ->withPivot(['id', 'role', 'is_club_admin', 'is_paid', 'joined_on', 'last_paid_on', 'ends_on'])
            ->withTimestamps();
    }

    public function membershipForClub(Club $club): ?ClubMembership
    {
        if ($this->relationLoaded('clubMemberships')) {
            return $this->clubMemberships->first(fn (ClubMembership $membership): bool => $membership->club_id === $club->id);
        }

        return $this->clubMemberships()->where('club_id', $club->id)->first();
    }

    public function mainClubMembership(): ?ClubMembership
    {
        return $this->mainClub ? $this->membershipForClub($this->mainClub) : null;
    }

    public function canAccessClub(Club $club): bool
    {
        if ($this->relationLoaded('clubs')) {
            return $this->clubs->contains(fn (Club $memberClub): bool => $memberClub->is($club));
        }

        return $this->clubs()->whereKey($club)->exists();
    }

    public function canAdministerClub(Club $club): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->clubMemberships()
            ->where('club_id', $club->id)
            ->where('is_club_admin', true)
            ->exists();
    }

    public function setMainClub(Club $club): void
    {
        abort_unless($this->canAccessClub($club), 404);

        $this->forceFill([
            'main_club_id' => $club->id,
        ])->save();

        $this->unsetRelation('mainClub');
    }

    public function syncMainClub(): void
    {
        if ($this->main_club_id !== null && $this->clubMemberships()->where('club_id', $this->main_club_id)->exists()) {
            return;
        }

        $fallbackClubId = $this->clubMemberships()
            ->orderBy('joined_on')
            ->orderBy('club_id')
            ->value('club_id');

        $this->forceFill([
            'main_club_id' => $fallbackClubId,
        ])->save();

        $this->unsetRelation('mainClub');
    }
}