<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public static function generatePaymentNumber()
    {
        $lastPayment = self::orderBy('id', 'desc')->first();
        $number = $lastPayment ? $lastPayment->id + 1 : 1;
        return 'PAY' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
