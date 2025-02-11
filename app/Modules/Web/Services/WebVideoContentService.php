<?php

namespace App\Modules\Web\Services;

use App\Enums\Status;
use App\Events\ContentAccessRequested;
use App\Events\ContentReported;
use App\Modules\Videos\Models\VideoModel;
use App\Modules\SearchHistories\Models\SearchHistory;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WebVideoContentService
{
    public function model(): Builder
    {
        return VideoModel::with(['CurrentUserFavourite', 'CurrentUserAccessible', 'CurrentUserReported', 'Languages'])->where('status', Status::Active->value());
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->defaultSort('-id')
            ->allowedSorts(['id', 'title'])
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
                AllowedFilter::callback('language', function (Builder $query, $value) {
                    $arr = array_map('intval', explode('_', $value));
                    $query->where(function($qry) use($arr){
                        $qry->whereHas('Languages', function($q) use($arr) {
                            $q->whereIn('language_id', $arr);
                        });
                    });
                }),
                AllowedFilter::callback('favourite', function (Builder $query, $value) {
                    if($value == 'yes'){
                        $query->where(function($qry){
                            $qry->whereHas('CurrentUserFavourite', function($q) {
                                $q->where('user_id', auth()->guard('web')->user()->id)->where('status', Status::Active->value());
                            });
                        });
                    }
                }),
            ]);
    }

    public function paginate(Int $total = 12): LengthAwarePaginator
	{
		return $this->query()
			->paginate($total)
			->appends(request()->query());
	}

    public function getByUuid(string $uuid): VideoModel
    {
        return $this->model()->where('uuid', $uuid)->firstOrFail();
    }

    public function getFileByUuid(string $uuid): VideoModel
    {
        return VideoModel::with(['CurrentUserAccessible'])->where('status', Status::Active->value())->where('uuid', $uuid)->firstOrFail();
    }

    public function searchHandler($search='')
    {
        $data = [];
        $datas = $this->query()->take(5)->get()->collect();

        foreach ($datas as $value) {
            if(!in_array(array("name"=>$value->title, "group"=>"Videos"), $data)){
                array_push($data,array("name"=>$value->title, "group"=>"Videos"));
            }
        }

        $tags = $this->model()->select('tags')->whereNotNull('tags')->where('tags', 'like', '%' . $search . '%')->take(5)->get()->collect();
        foreach ($tags as $tag) {
            $arr = explode(",",$tag->tags);
            foreach ($arr as $i) {
                if (!(in_array(array("name"=>$i, "group"=>"Tags"), $data))){
                    array_push($data,array("name"=>$i, "group"=>"Tags"));
                }
            }
        }

        $searchHistory = SearchHistory::where('screen', 2)->where('search', 'like', '%' . $search . '%')->take(5)->get()->collect();

        foreach ($searchHistory as $value) {
            if(!in_array(array("name"=>$value->search, "group"=>"Videos"), $data) && !in_array(array("name"=>$value->search, "group"=>"Tags"), $data)){
                array_push($data,array("name"=>$value->search, "group"=>"Previous Searches"));
            }
        }

        return $data;
    }
    
    public function toggleFavorite(VideoModel $data): void
    {
        if($data->CurrentUserFavourite){
            if($data->CurrentUserFavourite->status == Status::Active->value()){
                $data->CurrentUserFavourite()->update(['status' => Status::Inactive->value()]);
                $data->decrement('favourites');
            }else{
                $data->CurrentUserFavourite()->update(['status' => Status::Active->value()]);
                $data->increment('favourites');
            }
        }else{
            $data->CurrentUserFavourite()->create([
                'video_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Active->value(),
            ]);
            $data->increment('favourites');
        }
    }
    
    public function requestAccess(VideoModel $data, string $message): void
    {
        if($data->CurrentUserAccessible){
            if($data->CurrentUserAccessible->status == Status::Inactive->value()){
                $data->CurrentUserAccessible()->update([
                    'status' => Status::Inactive->value(),
                    'message' => $message
                ]);
                event(new ContentAccessRequested(
                    Auth::user()->name,
                    Auth::user()->email,
                    $data->title,
                    $data->uuid,
                    'video',
                    $message
                ));
            }
        }else{
            $data->CurrentUserAccessible()->create([
                'video_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Inactive->value(),
                'message' => $message
            ]);
            event(new ContentAccessRequested(
                Auth::user()->name,
                Auth::user()->email,
                $data->title,
                $data->uuid,
                'video',
                $message
            ));
        }
    }
    
    public function report(VideoModel $data, string $message): void
    {
        if($data->CurrentUserReported){
            if($data->CurrentUserReported->status == Status::Inactive->value()){
                $data->CurrentUserReported()->update([
                    'status' => Status::Inactive->value(),
                    'message' => $message
                ]);
                event(new ContentReported(
                    Auth::user()->name,
                    Auth::user()->email,
                    $data->title,
                    $data->uuid,
                    'video',
                    $message
                ));
                return;
            }
        }
        $data->CurrentUserReported()->create([
            'video_id' => $data->id,
            'user_id' => Auth::user()->id,
            'status' => Status::Inactive->value(),
            'message' => $message
        ]);
        event(new ContentReported(
            Auth::user()->name,
            Auth::user()->email,
            $data->title,
            $data->uuid,
            'video',
            $message
        ));
        return;
    }
}

class CommonFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function ($q) use ($value) {
            $q->where('title', 'like', '%' . $value . '%')
            ->orWhere('year', 'like', '%' . $value . '%')
            ->orWhere('deity', 'like', '%' . $value . '%')
            ->orWhere('version', 'like', '%' . $value . '%')
            ->orWhere('tags', 'like', '%' . $value . '%')
            ->orWhere('uuid', 'like', '%' . $value . '%');
        });
    }
}