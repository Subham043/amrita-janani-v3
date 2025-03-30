<?php

namespace App\Modules\Users\Controllers;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\Users\Requests\UserMultipleSelectionRequest;
use Illuminate\Support\Facades\DB;

class UserMultiDeleteController extends Controller
{

    
    public function index(UserMultipleSelectionRequest $request){
        $request->validated();
        $ids = $request->users;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one user to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            User::whereIn('id', $ids)->where('user_type', '!=', UserType::Admin)->forceDelete();
            return response()->json(["message"=>"User deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

}
