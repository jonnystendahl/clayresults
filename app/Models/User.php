<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'main_club_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
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
        return $this->hasMany(TrainingResult::class);
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(ClubRenewalRequest::class);
    }

    public function mainClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'main_club_id');
    }

    public function clubMemberships(): HasMany
    {
        return $this->hasMany(ClubMembership::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_memberships')
            ->withPivot(['id', 'role', 'is_paid', 'joined_on', 'last_paid_on', 'ends_on'])
            ->withTimestamps();
    }

    public function canAccessClub(Club $club): bool
    {
        if ($this->relationLoaded('clubs')) {
            return $this->clubs->contains(fn (Club $memberClub): bool => $memberClub->is($club));
        }

        return $this->clubs()->whereKey($club)->exists();
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
