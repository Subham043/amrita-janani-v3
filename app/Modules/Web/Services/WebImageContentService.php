<?php

namespace App\Modules\Web\Services;

use App\Enums\Status;
use App\Events\ContentAccessRequested;
use App\Events\ContentReported;
use App\Modules\Images\Models\ImageModel;
use App\Modules\SearchHistories\Models\SearchHistory;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WebImageContentService
{
    public function model(): Builder
    {
        return ImageModel::with(['CurrentUserFavourite', 'CurrentUserAccessible', 'CurrentUserReported'])->where('status', Status::Active->value());
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->defaultSort('-id')
            ->allowedSorts(['id', 'title'])
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
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
        defer(function(){
            $search = request()->query('filter')['search'] ?? NULL;
            if($search){
                SearchHistory::create([
                    'search' => $search,
                    'screen' => 5,
                    'user_id' => Auth::user()->id,
                ]);
            }
        });
		return $this->query()
			->paginate($total)
			->appends(request()->query());
	}

    public function getByUuid(string $uuid): ImageModel
    {
        return $this->model()->where('uuid', $uuid)->firstOrFail();
    }

    public function getFileByUuid(string $uuid): ImageModel
    {
        return ImageModel::with(['CurrentUserAccessible'])->where('status', Status::Active->value())->where('uuid', $uuid)->firstOrFail();
    }
    
    public function toggleFavorite(ImageModel $data): void
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
                'image_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Active->value(),
            ]);
            $data->increment('favourites');
        }
    }
    
    public function requestAccess(ImageModel $data, string $message): void
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
                    'image',
                    $message
                ));
            }
        }else{
            $data->CurrentUserAccessible()->create([
                'image_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Inactive->value(),
                'message' => $message
            ]);
            event(new ContentAccessRequested(
                Auth::user()->name,
                Auth::user()->email,
                $data->title,
                $data->uuid,
                'image',
                $message
            ));
        }
    }
    
    public function report(ImageModel $data, string $message): void
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
                    'image',
                    $message
                ));
                return;
            }
        }
        $data->CurrentUserReported()->create([
            'image_id' => $data->id,
            'user_id' => Auth::user()->id,
            'status' => Status::Inactive->value(),
            'message' => $message
        ]);
        event(new ContentReported(
            Auth::user()->name,
            Auth::user()->email,
            $data->title,
            $data->uuid,
            'image',
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