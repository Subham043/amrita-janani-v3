<?php

namespace App\Modules\Images\Services;

use App\Modules\Images\Models\ImageAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class ImageAccessService
{
    public function model(): Builder
    {
        return ImageAccess::with(['User', 'ImageModel']);
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

    public function getById(string $id): ImageAccess
	{
		return $this->model()->where('id', $id)->firstOrFail();
	}

    public function forceDelete(ImageAccess $model): ImageAccess
    {
        $model->forceDelete();
        return $model;
    }

    public function toggleStatus(array $data, ImageAccess $model): ImageAccess
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
            $q->whereHas('ImageModel', function($qr)  use ($value){
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
