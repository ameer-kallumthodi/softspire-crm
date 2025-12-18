<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'converted_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function purpose()
    {
        return $this->belongsTo(Purpose::class);
    }

    public function telecaller()
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function convertedBy()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
