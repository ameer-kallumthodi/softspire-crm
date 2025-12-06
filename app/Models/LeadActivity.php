<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LeadActivity extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'followup_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class);
    }
}

