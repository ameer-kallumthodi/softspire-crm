<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Purpose extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}

