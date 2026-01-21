<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
{

    protected $fillable = [
        'api_id',
        'name',
        'email',
        'contact_number',
        'api_created_at',
        'api_updated_at'
    ];

    protected $casts = [
        'api_created_at' => 'datetime',
        'api_updated_at' => 'datetime',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
