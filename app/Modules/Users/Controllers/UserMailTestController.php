<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Mails\SendAdminTestingEmail;
use Illuminate\Support\Facades\Mail;

class UserMailTestController extends Controller
{

    public function get(){
        try {
            //code...
            Mail::to(config('services.admin_email'))->send(new SendAdminTestingEmail);
            return redirect()->intended(route('subadmin_view'))->with('success_status', 'Email sent successfully.');
        } catch (\Throwable $th) {
            throw $th;
            return redirect()->intended(route('subadmin_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }
}
