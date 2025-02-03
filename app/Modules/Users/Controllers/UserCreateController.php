<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Requests\UserCreateRequest;
use App\Modules\Users\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Enums\UserStatus;
use App\Enums\UserType;

class UserCreateController extends Controller
{
    public function __construct(private UserService $userService){}

    public function get(){
        return view('pages.admin.user.create')
        ->with([
            'user_types' => Arr::mapWithKeys(UserType::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'user_statuses' => Arr::mapWithKeys(UserStatus::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
        ]);
    }

    public function post(UserCreateRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $this->userService->create([
                ...$request->validated(),
            ]);
            return redirect()->intended(route('subadmin_view'))->with('success_status', 'Data Stored successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->intended(route('subadmin_create'))->with('error_status', 'Something went wrong. Please try again');
        } finally {
            DB::commit();
        }
    }
}
