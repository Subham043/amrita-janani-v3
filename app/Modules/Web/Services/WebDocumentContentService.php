<?php

namespace App\Modules\Web\Services;

use App\Enums\Status;
use App\Events\ContentAccessRequested;
use App\Events\ContentReported;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\SearchHistories\Models\SearchHistory;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WebDocumentContentService
{
    public function model(): Builder
    {
        return DocumentModel::with(['CurrentUserFavourite', 'CurrentUserAccessible', 'CurrentUserReported', 'Languages'])->where('status', Status::Active->value());
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
        defer(function(){
            $search = request()->query('filter')['search'] ?? NULL;
            if($search){
                SearchHistory::create([
                    'search' => $search,
                    'screen' => 3,
                    'user_id' => Auth::user()->id,
                ]);
            }
        });
		return $this->query()
			->paginate($total)
			->appends(request()->query());
	}

    public function getByUuid(string $uuid): DocumentModel
    {
        return $this->model()->where('uuid', $uuid)->firstOrFail();
    }

    public function getFileByUuid(string $uuid): DocumentModel
    {
        return DocumentModel::with(['CurrentUserAccessible'])->where('status', Status::Active->value())->where('uuid', $uuid)->firstOrFail();
    }
    
    public function toggleFavorite(DocumentModel $data): void
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
                'document_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Active->value(),
            ]);
            $data->increment('favourites');
        }
    }
    
    public function requestAccess(DocumentModel $data, string $message): void
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
                    'document',
                    $message
                ));
            }
        }else{
            $data->CurrentUserAccessible()->create([
                'document_id' => $data->id,
                'user_id' => Auth::user()->id,
                'status' => Status::Inactive->value(),
                'message' => $message
            ]);
            event(new ContentAccessRequested(
                Auth::user()->name,
                Auth::user()->email,
                $data->title,
                $data->uuid,
                'document',
                $message
            ));
        }
    }
    
    public function report(DocumentModel $data, string $message): void
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
                    'document',
                    $message
                ));
                return;
            }
        }
        $data->CurrentUserReported()->create([
            'document_id' => $data->id,
            'user_id' => Auth::user()->id,
            'status' => Status::Inactive->value(),
            'message' => $message
        ]);
        event(new ContentReported(
            Auth::user()->name,
            Auth::user()->email,
            $data->title,
            $data->uuid,
            'document',
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