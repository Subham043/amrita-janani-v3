<?php

namespace App\Modules\Videos\Services;

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
        return VideoAccess::with(['User', 'VideoModel']);
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->defaultSort('-id')
            ->allowedSorts('id')
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
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
