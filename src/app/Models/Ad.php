<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ad extends Model
{
    protected $fillable = [
        'url',
        'olx_id',
        'last_price',
        'currency'
    ];

    public function emails(): BelongsToMany
    {
        return $this->belongsToMany(Email::class);
    }
}
