<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Requests\UserAccountPostRequest;
use App\Modules\Account\Requests\UserPasswordPostRequest;
use App\Modules\Account\Services\AccountService;
use Illuminate\Support\Facades\DB;

class UserAccountController extends Controller
{
    public function __construct(private AccountService $accountService){}


    public function index(){
        return view('pages.main.auth.user_profile')->with('breadcrumb','User Profile');
    }

    public function update(UserAccountPostRequest $request){
        DB::beginTransaction();
        try {
            //code...
            $request->user()->fill([
                ...$request->validated(),
            ]);
    
            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
                $request->user()->sendEmailVerificationNotification();
                $request->user()->save();
            }

            return response()->json(["message" => "Profile Updated successfully."], 201);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["error_popup"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }

    }

    public function profile_password(){
        return view('pages.main.auth.change_password')->with('breadcrumb','Change Password');
    }

    public function change_profile_password(UserPasswordPostRequest $request){
        DB::beginTransaction();
        try {
            //code...
            $this->accountService->update([
                ...$request->validated(),
            ]);

            return response()->json(["message" => "Password Updated successfully."], 201);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["error_popup"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }

    }


}
