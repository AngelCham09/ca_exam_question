<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{

    protected $fillable = [
        'api_id',
        'uid',
        'investor_id',
        'fund_id',
        'capital_amount',
        'start_date',
        'status',
        'api_created_at',
        'api_updated_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'capital_amount' => 'decimal:2',
        'api_created_at' => 'datetime',
        'api_updated_at' => 'datetime',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }
}
