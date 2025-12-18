<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}

