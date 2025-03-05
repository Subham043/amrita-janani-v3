<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Requests\AdminAccountPostRequest;
use App\Modules\Account\Requests\UserPasswordPostRequest;
use App\Modules\Account\Services\AccountService;
use Illuminate\Support\Facades\DB;

class AdminAccountController extends Controller
{
    public function __construct(private AccountService $accountService){}


    public function index(){
        return view('pages.admin.profile.index');
    }

    public function update(AdminAccountPostRequest $request){
        DB::beginTransaction();
        try {
            //code...
            $request->user()->fill([
                ...$request->validated(),
            ]);

            return response()->json(["url"=>empty($request->refreshUrl) ? route('profile') : $request->refreshUrl, "message" => "Profile Updated successfully."], 201);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["error_popup"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }

    }

    public function profile_password(UserPasswordPostRequest $request){
        DB::beginTransaction();
        try {
            //code...
            $this->accountService->update([
                ...$request->validated(),
            ]);

            return response()->json(["url"=>empty($request->refreshUrl) ? route('profile') : $request->refreshUrl, "message" => "Password Updated successfully."], 201);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["error_popup"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }

    }


}
