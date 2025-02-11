<?php

namespace App\Modules\Web\Services;

use App\Enums\Status;
use App\Events\ContentAccessRequested;
use App\Events\ContentReported;
use App\Modules\Audios\Models\AudioAccess;
use App\Modules\Audios\Models\AudioFavourite;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Audios\Models\AudioReport;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WebAudioContentService
{
    public function model(): Builder
    {
        return AudioModel::with(['AudioFavourite', 'AudioAccess', 'AudioReport'])->where('status', Status::Active->value());
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
                AllowedFilter::callback('favorite', function (Builder $query) {
                    $query->where(function($qry){
                        $qry->whereHas('AudioFavourite', function($q) {
                            $q->where('user_id', auth()->guard('web')->user()->id);
                        });
                    });
                }),
            ]);
    }

    public function paginate(Int $total = 10): LengthAwarePaginator
	{
		return $this->query()
			->paginate($total)
			->appends(request()->query());
	}

    public function getByUuid(string $uuid): AudioModel
    {
        return $this->model()->where('uuid', $uuid)->firstOrFail();
    }
    
    public function toggleFavorite(AudioModel $data): AudioModel
    {
        $fav = AudioFavourite::where('audio_id', $data->id)->where('user_id', Auth::user()->id)->first();
        if($fav){
            if($fav->status == Status::Active->value()){
                $fav->update(['status' => Status::Inactive->value()]);
                $data->decrement('favourites');
            }else{
                $fav->update(['status' => Status::Active->value()]);
                $data->increment('favourites');
            }
        }else{
            AudioFavourite::create([
                'audio_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Active->value(),
            ]);
            $data->increment('favourites');
            return $data;
        }
    }
    
    public function requestAccess(AudioModel $data, string $message): AudioModel
    {
        $access = AudioAccess::where('audio_id', $data->id)->where('user_id', Auth::user()->id)->first();
        if($access){
            if($access->status == Status::Active->value()){
                $access->update([
                    'status' => Status::Inactive->value(),
                    'message' => $message
                ]);
                event(new ContentAccessRequested(
                    Auth::user()->name,
                    Auth::user()->email,
                    $data->title,
                    $data->uuid,
                    'audio',
                    $message
                ));
            }
        }else{
            AudioAccess::create([
                'audio_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Inactive->value(),
                'message' => $message
            ]);
            event(new ContentAccessRequested(
                Auth::user()->name,
                Auth::user()->email,
                $data->title,
                $data->uuid,
                'audio',
                $message
            ));
            return $data;
        }
    }
    
    public function report(AudioModel $data, string $message): AudioModel
    {
        $report = AudioReport::where('audio_id', $data->id)->where('user_id', Auth::user()->id)->first();
        if($report){
            if($report->status == Status::Active->value()){
                $report->update([
                    'status' => Status::Inactive->value(),
                    'message' => $message
                ]);
                event(new ContentReported(
                    Auth::user()->name,
                    Auth::user()->email,
                    $data->title,
                    $data->uuid,
                    'audio',
                    $message
                ));
            }
        }else{
            AudioReport::create([
                'audio_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Inactive->value(),
                'message' => $message
            ]);
            event(new ContentReported(
                Auth::user()->name,
                Auth::user()->email,
                $data->title,
                $data->uuid,
                'audio',
                $message
            ));
            return $data;
        }
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