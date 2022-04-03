<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadminProfileCompleteness extends Model
{
    protected $table = 'rest_admins_profile_completeness';

    protected $primaryKey = 'radmin_id';

    public $timestamps = false;
    
    
    public function radmin()
    {
        return $this->belongsTo(RestAdmin::class, 'id', 'radmin_id');
    }

    public function reCalculate(RestAdmin $radmin){
        if ($radmin->restaurant){
            $this->is_restaurant_added = true;
        }

        if ($radmin->name != null && $radmin->contact_no != null && $radmin->restaurant && $radmin->restaurant->registration_number != null){
            $this->is_name_added = true;
        }

        $this->save();
    }
    
    public function isNotComplete(){
        if ($this->is_registered && $this->is_restaurant_added && $this->is_name_added){
            return false;
        }
        return true;
    }

    public function getRedirectLink(){
        if (!$this->is_restaurant_added){
            return route('radmin.profile.addBusiness');
        } else if (!$this->is_name_added){
            return route('radmin.profile.addContact');
        }

        return route('home');
    }
    
    public static function get(RestAdmin $radmin){
        $pc = $radmin->profileComplete;
        if (!$pc){
            $radmin->profileComplete()->insert([
                'radmin_id' => $radmin->id
            ]);
        }

        return $radmin->profileComplete()->first();
    }
}
