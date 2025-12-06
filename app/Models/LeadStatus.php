<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}

