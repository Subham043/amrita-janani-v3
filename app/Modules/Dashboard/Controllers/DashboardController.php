<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Enquiries\Models\Enquiry;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Users\Models\User;
use App\Modules\Videos\Models\VideoModel;

class DashboardController extends Controller
{

    public function index(){
        return view('pages.admin.dashboard.index')
        ->with([
            'user_count'=>User::count(),
            'enquiry_count'=>Enquiry::count(),
            'media_count'=>ImageModel::count()+AudioModel::count()+DocumentModel::count()+VideoModel::count()
        ]);
    }

}
