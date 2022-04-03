<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Model;

class MobileCalls extends Model
{
    use StatsHelper;
    
    protected $table = 'stats_mobile_calls';
}
