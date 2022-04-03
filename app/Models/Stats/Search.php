<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use StatsHelper;
    
    protected $table = 'stats_search';
}
