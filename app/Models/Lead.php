<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'followup_date' => 'date',
        'first_created_at' => 'datetime',
        'is_meta' => 'boolean',
        'is_converted' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function purpose()
    {
        return $this->belongsTo(Purpose::class);
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class);
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function telecaller()
    {
        return $this->belongsTo(User::class, 'telecaller_id');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('leadStatus', function($q) {
            $q->where('status', 'active');
        });
    }
}
