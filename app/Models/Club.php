<?php

namespace App\Models;

use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    /** @use HasFactory<ClubFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone',
        'note',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMembership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_memberships')
            ->withPivot(['id', 'role', 'is_club_admin', 'is_paid', 'joined_on', 'last_paid_on', 'ends_on'])
            ->withTimestamps();
    }

    public function newsPosts(): HasMany
    {
        return $this->hasMany(ClubNewsPost::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ClubEvent::class);
    }

    public function boardMembers(): HasMany
    {
        return $this->hasMany(ClubBoardMember::class)->orderBy('sort_order')->orderBy('name');
    }

    public function renewalSetting(): HasOne
    {
        return $this->hasOne(ClubRenewalSetting::class);
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(ClubRenewalRequest::class);
    }
}