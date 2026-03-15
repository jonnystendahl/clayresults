<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingResult extends Model
{
    use HasFactory;

    public const DISCIPLINES = [
        'Nordisk Trap',
        'Automat Trap',
        'Olympisk Skeet',
        'Nationell Skeet',
        'Compak Sporting',
        'English Sporting',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'club_id',
        'performed_on',
        'discipline',
        'score',
        'note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performed_on' => 'date',
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
}