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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}