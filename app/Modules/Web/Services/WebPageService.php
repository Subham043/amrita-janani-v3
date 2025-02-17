<?php

namespace App\Modules\Web\Services;

use App\Enums\DarkMode;
use App\Enums\Status;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Banners\Models\BannerModel;
use App\Modules\Banners\Models\BannerQuoteModel;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Enquiries\Models\Enquiry;
use App\Modules\FAQs\Models\FAQModel;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Pages\Models\PageModel;
use App\Modules\SearchHistories\Models\SearchHistory;
use App\Modules\Videos\Models\VideoModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class WebPageService
{
    public function getData(string $slug): PageModel
    {
        return PageModel::where('url', $slug)->firstOrFail();
    }
    
    public function getBannerImage(): BannerModel
    {
        return BannerModel::inRandomOrder()->firstOrFail();
    }
    
    public function getBannerQuote(): BannerQuoteModel
    {
        return BannerQuoteModel::inRandomOrder()->firstOrFail();
    }
    
    public function getFaqs(): Collection
    {
        return FAQModel::lazy(100)->collect();
    }
    
    public function getLanguages(): Collection
    {
        return LanguageModel::lazy(100)->collect();
    }
    
    public function createEnquiry(array $data): Enquiry
    {
        return Enquiry::create($data);
    }

    public function toggleDarkMode(): void
    {
        $user = request()->user();
        $user->update(
            [
                'dark_mode' => $user->dark_mode == DarkMode::No->value() ? DarkMode::Yes->value() : DarkMode::No->value(),
            ]
        );
    }

    public function searchHistoryList(): LengthAwarePaginator
    {
        return SearchHistory::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->paginate(10)->appends(request()->query());
    }

    public function imageSearchQuery(string $search)
    {
        return ImageModel::query()->selectRaw('title, tags, "IMAGE" as type')->where('status', Status::Active->value())->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')->orWhere('tags', 'like', '%' . $search . '%');
        })->take(3);
    }
    
    public function audioSearchQuery(string $search)
    {
        return AudioModel::query()->selectRaw('title, tags, "AUDIO" as type')->where('status', Status::Active->value())->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')->orWhere('tags', 'like', '%' . $search . '%');
        })->take(3);
    }
    
    public function documentSearchQuery(string $search)
    {
        return DocumentModel::query()->selectRaw('title, tags, "DOCUMENT" as type')->where('status', Status::Active->value())->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')->orWhere('tags', 'like', '%' . $search . '%');
        })->take(3);
    }
    
    public function videoSearchQuery(string $search)
    {
        return VideoModel::query()->selectRaw('title, tags, "VIDEO" as type')->where('status', Status::Active->value())->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')->orWhere('tags', 'like', '%' . $search . '%');
        })->take(3);
    }
    
    public function searchHistoryQuery(string $search, int $screen = 1)
    {
        return SearchHistory::query()->selectRaw('search as title, "" as tags, "PREVIOUS SEARCHES" as type')->where('screen', $screen)->where(function ($query) use ($search) {
            $query->where('search', 'like', '%' . $search . '%');
        })->take(3);
    }

    public function searchQueryList(string $search = '')
    {
        $data = [];

        $imageQuery = $this->imageSearchQuery($search);
        $audioQuery = $this->audioSearchQuery($search);
        $documentQuery = $this->documentSearchQuery($search);
        $videoQuery = $this->videoSearchQuery($search);
        $searchQuery = $this->searchHistoryQuery($search, 1);

        $query = $imageQuery->unionAll($audioQuery)->unionAll($documentQuery)->unionAll($videoQuery)->unionAll($searchQuery);

        $datas = $query->get();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>$value->type), $data)){
                array_push($data,array("name"=>$value->title, "group"=>$value->type));
            }
        }
        
        foreach ($datas as $value) {
            $arr = explode(",",$value->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"TAGS"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"TAGS"));
                }
            }
        }

        return $data;
    }
    
    public function audioSearchQueryList(string $search = '')
    {
        $data = [];

        $audioQuery = $this->audioSearchQuery($search);
        $searchQuery = $this->searchHistoryQuery($search, 2);

        $query = $audioQuery->unionAll($searchQuery);

        $datas = $query->get();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>$value->type), $data)){
                array_push($data,array("name"=>$value->title, "group"=>$value->type));
            }
        }
        
        foreach ($datas as $value) {
            $arr = explode(",",$value->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"TAGS"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"TAGS"));
                }
            }
        }

        return $data;
    }
    public function documentSearchQueryList(string $search = '')
    {
        $data = [];

        $documentQuery = $this->documentSearchQuery($search);
        $searchQuery = $this->searchHistoryQuery($search, 3);

        $query = $documentQuery->unionAll($searchQuery);

        $datas = $query->get();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>$value->type), $data)){
                array_push($data,array("name"=>$value->title, "group"=>$value->type));
            }
        }
        
        foreach ($datas as $value) {
            $arr = explode(",",$value->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"TAGS"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"TAGS"));
                }
            }
        }

        return $data;
    }
    public function videoSearchQueryList(string $search = '')
    {
        $data = [];

        $videoQuery = $this->videoSearchQuery($search);
        $searchQuery = $this->searchHistoryQuery($search, 4);

        $query = $videoQuery->unionAll($searchQuery);

        $datas = $query->get();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>$value->type), $data)){
                array_push($data,array("name"=>$value->title, "group"=>$value->type));
            }
        }
        
        foreach ($datas as $value) {
            $arr = explode(",",$value->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"TAGS"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"TAGS"));
                }
            }
        }

        return $data;
    }
    
    public function imageSearchQueryList(string $search = '')
    {
        $data = [];

        $imageQuery = $this->imageSearchQuery($search);
        $searchQuery = $this->searchHistoryQuery($search, 5);

        $query = $imageQuery->unionAll($searchQuery);

        $datas = $query->get();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>$value->type), $data)){
                array_push($data,array("name"=>$value->title, "group"=>$value->type));
            }
        }
        
        foreach ($datas as $value) {
            $arr = explode(",",$value->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"TAGS"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"TAGS"));
                }
            }
        }

        return $data;
    }
}