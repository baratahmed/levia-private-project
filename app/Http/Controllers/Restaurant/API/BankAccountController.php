<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Models\RestBankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();


        $accounts = $rest->bank_account()->get();

        return response([
            'success' => 'true',
            'data'    => $accounts,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'bank_name' => 'required',
            'account_holder_name' => 'required',
            'branch_name' => 'required',
            'account_number' => 'required'
        ]);

        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();


        $existing_method = $rest->bank_account()
            ->where('bank_name', $request->input('bank_name'))
            ->where('account_holder_name', $request->input('account_holder_name'))
            ->where('branch_name', $request->input('branch_name'))
            ->where('account_number', $request->input('account_number'))
            ->first();

        if ($existing_method){
            throw ValidationException::withMessages([
                'type' => 'You already have this "' . $request->input('bank_name') . '" account added on your account.'
            ]);
        }

        $account = new RestBankAccount([
            'bank_name' => $request->input('bank_name'),
            'account_holder_name' => $request->input('account_holder_name'),
            'branch_name' => $request->input('branch_name'),
            'account_number' => $request->input('account_number')
        ]);


        $rest->bank_account()->save($account);

        return response([
            'success' => 'true',
            'data'    => $account,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RestBankAccount $account)
    {
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // return $account;

        if ( (int) $account->rest_id !== (int) $rest->id ){
            abort(401, "Unauthorized");
        }

        $account->update($request->only([
            'bank_name',
            'account_holder_name',
            'branch_name',
            'account_number',
        ]));

        return response([
            'success' => 'true',
            'data'    => $account,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RestBankAccount $account)
    {
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // return $account;

        if ( (int) $account->rest_id !== (int) $rest->id ){
            abort(401, "Unauthorized");
        }

        $account->delete();

        return response([
            'success' => 'true'
        ], 200);
    }
}
