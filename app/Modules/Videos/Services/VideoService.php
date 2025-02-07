<?php

namespace App\Modules\Videos\Services;

use App\Abstracts\AbstractExcelService;
use App\Modules\Videos\Models\VideoModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;

class VideoService extends AbstractExcelService
{
    public function model(): Builder
    {
        return VideoModel::with(['User']);
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

    public function getByUuid(string $uuid): VideoModel
	{
		return $this->model()->where('uuid', $uuid)->firstOrFail();
	}
    
    public function getTrashedByUuid(string $uuid): VideoModel
	{
		return $this->model()->withTrashed()->where('uuid', $uuid)->firstOrFail();
	}

    public function excel(): SimpleExcelWriter
    {
        $model = $this->query();
        $i = 0;
        $writer = SimpleExcelWriter::streamDownload('images.xlsx');
        foreach ($model->lazy(1000)->collect() as $data) {
            $writer->addRow([
                'Id' => $data->id,
                'Title' => $data->title,
                'UUID' => $data->uuid,
                'Description' => $data->description_unformatted,
                'Year' => $data->year,
                'Deity' => $data->deity,
                'Version' => $data->version,
                'Favourites' => $data->favourites,
                'Views' => $data->views,
                'File' => $data->image,
                'Status' => $data->status==1 ? 'Active' : 'Inactive',
                'Restricted' => $data->restricted==1 ? 'Yes' : 'No',
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
            $q->where('title', 'like', '%' . $value . '%')
            ->orWhere('year', 'like', '%' . $value . '%')
            ->orWhere('deity', 'like', '%' . $value . '%')
            ->orWhere('version', 'like', '%' . $value . '%')
            ->orWhere('tags', 'like', '%' . $value . '%')
            ->orWhere('uuid', 'like', '%' . $value . '%');
        });
    }
}
