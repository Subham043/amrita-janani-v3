<?php

namespace App\Modules\Dashboard\Services;

use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Enquiries\Models\Enquiry;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Users\Models\User;
use App\Modules\Videos\Models\VideoModel;


class DashboardService
{

    public function __construct(private ResultStore $resultStore){}

    public function getAppHealthResult(){

        //code taken from spatie health
        if (request()->has('fresh')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $this->resultStore->latestResults();

        return $checkResults;
    }

    public function getUserCount()
    {
        return User::count();
    }

    public function getEnquiryCount()
    {
        return Enquiry::count();
    }

    public function getMediaCount()
    {
        return ImageModel::count()+AudioModel::count()+DocumentModel::count()+VideoModel::count();
    }


}