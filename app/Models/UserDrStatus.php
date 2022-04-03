<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDrStatus extends Model
{
    protected $guarded = [];
    protected $table = 'user_dr_status';
    
    /**
     * Get or Create Delivery Rep Status
     *
     * @param  User $user
     * @return UserDrStatus
     */
    public static function getOrCreate(User $user){
        $status = static::where('user_id', $user->id)->first();

        if ( ! $status){
            if ($user->isDeliveryRep){
                $status = new static([
                    'user_id' => $user->id
                ]);
                $status->save();
            } else {
                abort(500, 'User is not a Delivery Representative');
            }
        }

        return $status;
    }
}
