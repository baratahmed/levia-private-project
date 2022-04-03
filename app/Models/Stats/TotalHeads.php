<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Model;

class TotalHeads extends Model
{
    use StatsFilter;

    protected $table = "stats_heads";
}
