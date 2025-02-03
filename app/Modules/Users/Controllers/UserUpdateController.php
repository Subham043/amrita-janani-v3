<?php

namespace App\Modules\Users\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Modules\Users\Requests\UserUpdateRequest;
use App\Modules\Users\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class UserUpdateController extends Controller
{
    public function __construct(private UserService $userService){}

    public function get($id){
        $user = $this->userService->getById($id);
        return view('pages.admin.user.edit')
        ->with('data',$user)
        ->with([
            'user_types' => Arr::mapWithKeys(UserType::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'user_statuses' => Arr::mapWithKeys(UserStatus::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
        ]);
    }

    public function post(UserUpdateRequest $request, $id){
        $user = $this->userService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->userService->update([
                ...$request->validated(),
            ], $user);
            return redirect()->intended(route('subadmin_edit',$user->id))->with('success_status', 'Data Updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->intended(route('subadmin_edit',$user->id))->with('error_status', 'Something went wrong. Please try again');
        } finally {
            DB::commit();
        }
    }
}
