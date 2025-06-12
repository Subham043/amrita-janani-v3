<?php

namespace App\Modules\Audios\Services;

use App\Modules\Audios\Models\AudioReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class AudioReportService
{
    public function model(): Builder
    {
        return AudioReport::with(['User', 'AudioModel'])->whereHas('User')->whereHas('AudioModel');
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
                        $query->where('status',$value);
                    }
                }),
            ]);
    }

    public function paginate(Int $total = 10): LengthAwarePaginator
    {
        return $this->query()->paginate($total)->appends(request()->query());
    }

    public function getById(string $id): AudioReport
	{
		return $this->model()->where('id', $id)->firstOrFail();
	}

    public function forceDelete(AudioReport $model): AudioReport
    {
        $model->forceDelete();
        return $model;
    }

    public function toggleStatus(array $data, AudioReport $model): AudioReport
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
            $q->whereHas('AudioModel', function($qr)  use ($value){
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
