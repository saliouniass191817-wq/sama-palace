<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorVisit extends Model
{
    protected $fillable = [
        'visitor_hash',
        'visited_on',
        'scope_key',
        'shop_id',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
        ];
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
