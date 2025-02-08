<?php

namespace App\Modules\Pages\Services;

use App\Modules\Pages\Models\PageContentModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PageContentService
{
    public function model(): Builder
    {
        return PageContentModel::query();
    }

    public function query(string $page_id): QueryBuilder
    {
        return QueryBuilder::for($this->model()->where('page_id', $page_id))
            ->defaultSort('-id')
            ->allowedSorts('id')
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
            ]);
    }

    public function all(string $page_id): Collection
	{
		return $this->query($page_id)->lazy(100)->collect();
	}

	public function paginate(string $page_id, Int $total = 10): LengthAwarePaginator
	{
		return $this->query($page_id)
			->paginate($total)
			->appends(request()->query());
	}

	public function getById(Int $id): PageContentModel
	{
		return $this->model()->findOrFail($id);
	}

	public function create(array $data): PageContentModel
	{
		return $this->model()->create($data);
	}

	public function update(array $data, $model): PageContentModel
	{
		$model->update($data);
		$model->refresh();
		return $model;
	}

	public function delete($model): bool
	{
		return $model->delete();
	}

	public function forceDelete($model): bool
	{
		return $model->forceDelete();
	}

    public function excel(string $page_id): SimpleExcelWriter
    {
        $model = $this->query($page_id);
        $i = 0;
        $writer = SimpleExcelWriter::streamDownload('page_contents.xlsx');
        foreach ($model->lazy(1000)->collect() as $data) {
            $writer->addRow([
                'Id' => $data->id,
                'Heading' => $data->heading,
                'Description' => $data->description_unformatted,
                'Page Id' => $data->page_id,
                'Created At' => $data->created_at->format('Y-m-d H:i:s'),
            ]);
            if ($i == 1000) {
                flush();
            }
            $i++;
        }
        return $writer;
    }
}

class CommonFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function ($q) use ($value) {
            $q->where('heading', 'LIKE', '%' . $value . '%')
            ->orWhere('description_unformatted', 'LIKE', '%' . $value . '%');
        });
    }
}
