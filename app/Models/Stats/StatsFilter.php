<?php

namespace App\Models\Stats;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait StatsFilter {
    private $duration, $originalDuration;

    private function getValidDuration($duration){
        $this->originalDuration = $this->duration = static::getValidDurationString($duration);

        return $this->duration;
    }
    
    public static function getValidDurationString($duration){
        $validDurations = ['padding', 'today', 'last_week', 'week', 'month', '3months', '6months', 'year', 'lifetime'];

        if (!array_search($duration, $validDurations)){
            $duration = 'today';
        }

        return $duration;
    }

    public function scopeFilterDuration($query, $duration){
        $duration = $this->getValidDuration($duration);

        // return $duration;

        switch ($duration) {
            case 'today':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today());
                break;
            case 'last_week':
                return $query->where($this->getTable().'.created_at', '>', Carbon::today()->subDays(14))->where($this->getTable().'.created_at', '<=', Carbon::today()->subDays(7));
                break;
            case 'week':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today()->subDays(7));
                break;
            case 'month':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today()->subMonth());
                break;
            case '3months':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today()->subMonths(3));
                break;
            case '6months':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today()->subMonths(6));
                break;
            case 'year':
                return $query->where($this->getTable().'.created_at', '>=', Carbon::today()->subYear());
                break;

            default :
                return $query;
                break;
        }
    }

    public function scopeStats($query, $duration = null){
        if ($this->originalDuration == null){
            return static::filterDuration('today')->stats();
        }

        $duration = $duration == null ? $this->duration : $this->getValidDuration($duration);
        $table_name = $this->getTable();

        switch ($duration) {
            case 'today':
                return $query->groupBy(DB::raw('concat(month('.$table_name.'.created_at), "-", day('.$table_name.'.created_at))'))
                        ->selectRaw('concat(MONTHNAME('.$table_name.'.created_at), "-", day('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('month('.$table_name.'.created_at) asc, day('.$table_name.'.created_at)'));
                break;
            case 'last_week':
                return $query->groupBy(DB::raw('concat(month('.$table_name.'.created_at), "-", day('.$table_name.'.created_at))'))
                        ->selectRaw('concat(MONTHNAME('.$table_name.'.created_at), "-", day('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('month('.$table_name.'.created_at) asc, day('.$table_name.'.created_at)'));
                break;
            case 'week':
                return $query->groupBy(DB::raw('concat(month('.$table_name.'.created_at), "-", day('.$table_name.'.created_at))'))
                        ->selectRaw('concat(MONTHNAME('.$table_name.'.created_at), "-", day('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('month('.$table_name.'.created_at) asc, day('.$table_name.'.created_at)'));
                break;
            case 'month':
                return $query->groupBy(DB::raw('concat(month('.$table_name.'.created_at), "-", day('.$table_name.'.created_at))'))
                        ->selectRaw('concat(MONTHNAME('.$table_name.'.created_at), "-", day('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('month('.$table_name.'.created_at) asc, day('.$table_name.'.created_at)'));
                break;
            case '3months':
                return $query->groupBy(DB::raw('concat(year('.$table_name.'.created_at), month('.$table_name.'.created_at), "-", week('.$table_name.'.created_at))'))
                        ->selectRaw('concat(MONTHNAME('.$table_name.'.created_at), "-Week: ", week('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('month('.$table_name.'.created_at) asc, week('.$table_name.'.created_at)'));
                break;
            case '6months':
                return $query->groupBy(DB::raw('concat(year('.$table_name.'.created_at), "-", month('.$table_name.'.created_at))'))
                        ->selectRaw('concat(monthname('.$table_name.'.created_at), "-", year('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('year('.$table_name.'.created_at) asc, month('.$table_name.'.created_at)'));
                break;
            case 'year':
                return $query->groupBy(DB::raw('concat(year('.$table_name.'.created_at), "-", month('.$table_name.'.created_at))'))
                        ->selectRaw('concat(monthname('.$table_name.'.created_at), "-", year('.$table_name.'.created_at)) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('year('.$table_name.'.created_at) asc, month('.$table_name.'.created_at)'));
                break;
            case 'lifetime' :
                return $query->groupBy(DB::raw('concat(year('.$table_name.'.created_at))'))
                        ->selectRaw('year('.$table_name.'.created_at) as needle, count('.$table_name.'.id) as count')
                        ->orderBy(DB::raw('year('.$table_name.'.created_at)'));
                break;
            default :
                return static::filterDuration('week')->stats();
                break;
        }
    }

    public static function addZeroValues($data, $duration = null){
        $duration = static::getValidDurationString($duration);

        $needles = [];
        $updatedData = [];
        $today = Carbon::now();

        switch ($duration) {
            case 'today':
                break;
            case 'last_week':
                for($i=13; $i >= 7; $i--){
                    $that_day = $today->copy();
                    $that_day->subDays($i);
                    $needles[] = $that_day->format("F-d");
                }
                break;
            case 'week':
                for($i=6; $i >= 0; $i--){
                    $that_day = $today->copy();
                    $that_day->subDays($i);
                    $needles[] = $that_day->format("F-d");
                }
                break;
            case 'month':
                break;
            case '3months':
                break;
            case '6months':
                break;
            case 'year':
                break;
            default :
                break;
        }

        // Add zeroes or original values in new array
        foreach( $needles as $needle ){
            $needles_data = $data->where('needle', $needle)->first();
            if ( $needles_data ){
                $updatedData[] = [
                    'needle' => $needle,
                    'count' => $needles_data->count,
                    'revenue' => $needles_data->total,
                ];
            } else {
                $updatedData[] = [
                    'needle' => $needle,
                    'count' => "0",
                    'revenue' => "0.00",
                ];
            }
        }

        // dd("done");

        return $updatedData;
    }
}