<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $guarded = [];

    public function scopeWithoutTimestamps()
    {
        $this->timestamps = false;
        return $this;
    }
}
