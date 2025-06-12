<?php

namespace App\Modules\Videos\Services;

use App\Enums\UserType;
use App\Modules\Videos\Models\VideoAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class VideoAccessService
{
    public function model(): Builder
    {
        return VideoAccess::with(['User', 'VideoModel'])->whereHas('User')->whereHas('VideoModel');
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->defaultSort('-id')
            ->allowedSorts('id')
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
                AllowedFilter::callback('status', function (Builder $query, $value) {
                    if($value != 'all') {
                        $query->where(function($qr) use($value){
                            if($value=='1'){
                                $qr->where('status',$value)->orWhere(function($q){
                                    $q->whereHas('User', function($q){
                                        $q->where('user_type', '!=', UserType::User->value());
                                    });
                                });
                            }else{
                                $qr->where('status',$value)->where(function($q){
                                    $q->whereHas('User', function($q){
                                        $q->where('user_type', UserType::User->value());
                                    });
                                });
                            }
                        });
                    }
                }),
            ]);
    }

    public function paginate(Int $total = 10): LengthAwarePaginator
    {
        return $this->query()->paginate($total)->appends(request()->query());
    }

    public function getById(string $id): VideoAccess
	{
		return $this->model()->where('id', $id)->firstOrFail();
	}

    public function forceDelete(VideoAccess $model): VideoAccess
    {
        $model->forceDelete();
        return $model;
    }

    public function toggleStatus(array $data, VideoAccess $model): VideoAccess
    {
        $model->update($data);
        $model->refresh();
        return $model;
    }
}

class CommonFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function ($q) use ($value) {
            $q->whereHas('VideoModel', function($qr)  use ($value){
                $qr->where('title', 'like', '%' . $value . '%')
                ->orWhere('uuid', 'like', '%' . $value . '%');
            })
            ->orWhereHas('User', function($q)  use ($value){
                $q->where('name', 'like', '%' . $value . '%')
                ->orWhere('email', 'like', '%' . $value . '%');
            });
        });
    }
}
