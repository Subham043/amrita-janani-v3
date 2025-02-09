<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;

class PrivacyPolicyPageController extends Controller
{

    public function get(){
        return view('pages.main.privacy_policy')->with('breadcrumb','Privacy Policy');
    }
}
