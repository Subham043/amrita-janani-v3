<?php

namespace App\Modules\Account\Controllers;

use App\Enums\Restricted;
use App\Http\Controllers\Controller;
use App\Modules\Account\Requests\SetupSocialAccountPostRequest;
use App\Modules\Account\Services\AccountService;
use Illuminate\Http\Request;

class SetupSocialAccountController extends Controller
{
    public function __construct(private AccountService $accountService){}


    public function get(Request $request){
        if($request->user()->hasCompletedAccountSetup()){
            return redirect()->intended(route('content_dashboard'))->with('success_status', 'Oops! you have already set up your account.');
        }
        return view('pages.main.auth.setup_user')->with('breadcrumb','Setup Account');
    }

    public function post(SetupSocialAccountPostRequest $request){
        if($request->user()->hasCompletedAccountSetup()){
            return redirect()->intended(route('content_dashboard'))->with('success_status', 'Oops! you have already set up your account.');
        }
        $this->accountService->update([
            ...$request->safe()->except(['g-recaptcha-response']),
            'is_social' => Restricted::No->value(),
        ]);
        return redirect()->intended(route('content_dashboard'))->with('success_status', 'Completed account setup successfully.');
    }


}
