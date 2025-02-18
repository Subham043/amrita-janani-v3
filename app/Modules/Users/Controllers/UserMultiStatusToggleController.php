<?php

namespace App\Modules\Users\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\UserMultipleSelectionRequest;
use Illuminate\Support\Facades\DB;

class UserMultiStatusToggleController extends Controller
{

    
    public function index(UserMultipleSelectionRequest $request){
        $request->validated();
        $ids = $request->users;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one user to toggle status."], 400);
        }

        $status = $request->query('status')==UserStatus::Blocked->value ? UserStatus::Blocked->value : UserStatus::Active->value;

        DB::beginTransaction();

        try {
            //code...
            User::whereIn('id', $ids)->where('user_type', '!=', UserType::Admin)->update(['status' => $status]);
            return response()->json(["message"=>"Updated user status successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

}
