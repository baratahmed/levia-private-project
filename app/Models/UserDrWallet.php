<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDrWallet extends Model
{
    protected $table = 'user_dr_wallet';

    protected $guarded = [];

    /**
     * Get or Create Delivery Rep Wallet
     *
     * @param  User $user
     * @return UserDrWallet
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
    
    /**
     * Deduct balance from the representative's wallet
     *
     * @param  mixed $amount
     * @return void
     */
    public function deductBalance($amount){
        $this->balance -= $amount;
        $this->save();
    }
    
    
    /**
     * Add balance from the representative's wallet
     *
     * @param  mixed $amount
     * @return void
     */
    public function addBalance($amount){
        $this->balance += $amount;
        $this->save();
    }
}
