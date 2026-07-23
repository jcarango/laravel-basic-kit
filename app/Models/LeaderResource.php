<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_id',
        'type',
        'concept',
        'quantity',
        'value',
        'status',
        'delivery_date',
        'user_id',
        'responsible_person',
        'description',
        'attachment_path',
        'observations',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'value' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class, 'leader_id')->where('is_leader', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
