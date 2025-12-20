<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'quotation_date' => 'date',
        'total_amount' => 'decimal:2',
        'annual_amount' => 'decimal:2',
        'is_accepted' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateQuotationNumber()
    {
        $lastQuotation = self::orderBy('id', 'desc')->first();
        $number = $lastQuotation ? $lastQuotation->id + 1 : 1;
        return 'QT' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

