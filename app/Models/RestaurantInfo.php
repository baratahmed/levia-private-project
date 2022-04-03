<?php

namespace App\Models;

use App\LeviaHelpers\UserBookmarks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class RestaurantInfo extends Model
{
	use SoftDeletes;
	protected $table = 'rest_info';
	protected $guarded = ['_token', "email"];
	protected $appends = ['imageUrl', 'rating', 'district_name'];

	public function district(){
		return $this->hasOne(District::class, 'district_id', 'district_id');
	}

	public function admin(){
		return $this->belongsTo(RestAdmin::class, 'radmin_id', 'id');
	}

	public function food(){
		return $this->hasMany(RestFood::class, 'rest_id', 'id');
	}
	
	public function schedule(){
		return $this->hasMany(RestSchedule::class, 'rest_id', 'id');
	}

	public function reservations(){
		return $this->hasMany(Reservation::class, 'rest_id', 'id');
	}

	public function paymethod(){
		return $this->hasOne(RestPaymentMethod::class, 'rest_id', 'id');
	}

	public function properties(){
		return $this->hasOne(RestProperty::class, 'rest_id', 'id');
	}

	public function bank_account(){
		return $this->hasMany(RestBankAccount::class, 'rest_id', 'id');
	}

	public function getPaymentMethods(){
		if ($this->paymethod != null){
			return $this->paymethod;
		} else {
			return new RestPaymentMethod();
		}
	}

	public function scopeOnlyPublished($query){
		$query->where('is_published', true)->where('deleted_at', null);
	}

	public function scopeOnlyUnpublished($query){
		$query->where('is_published', false);
	}

	public function getImageUrlAttribute(){
		if ($this->rest_image_url == null){
			return asset('storage/rest_logo/default.jpg');
		}
		return asset('storage/rest_logo/'. $this->rest_image_url);
	}

	public function getDistrictNameAttribute(){
		$district = District::where('district_id', $this->district_id)->first();
		if ($district){
			return $district->district_name;
		}
		return null;
	}

	public function getRestOrUserAttribute(){
        return "REST";
    }

	public function getIsBookmarkedAttribute(){
		$user = auth('api')->user();
		return UserBookmarks::get($user)->contains($this);
	}

	public function getRatingAttribute(){
		// TODO: Recalculate and store for future reference.

		return DB::table('rest_rating_review_dataset')->selectRaw("avg(rest_rating_value) as rating, count(id) as count, count(has_review) as review_count, review_text ")->where('rest_id', $this->id)->get();
	}


	public function authorizeReceiveOrder(){
		return "Finix" === $this->plan;
	}
}
