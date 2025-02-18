<?php

namespace App\Modules\Audios\Services;

use App\Abstracts\AbstractExcelService;
use App\Modules\Audios\Models\AudioModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;

class AudioService extends AbstractExcelService
{
    public function model(): Builder
    {
        return AudioModel::with(['User']);
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
                AllowedFilter::callback('restricted', function (Builder $query, $value) {
                    if($value != 'all') {
                        $query->where('restricted',$value);
                    }
                }),
            ]);
    }

    public function getByUuid(string $uuid): AudioModel
	{
		return $this->model()->where('uuid', $uuid)->firstOrFail();
	}
    
    public function getTrashedByUuid(string $uuid): AudioModel
	{
		return $this->model()->withTrashed()->where('uuid', $uuid)->firstOrFail();
	}

    public function excel(): SimpleExcelWriter
    {
        $model = $this->query();
        $i = 0;
        $writer = SimpleExcelWriter::streamDownload('audios.xlsx');
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
                'File' => $data->audio,
                'Status' => $data->status==1 ? 'Active' : 'Inactive',
                'Restricted' => $data->restricted==1 ? 'Yes' : 'No',
                'Created At' => $data->created_at ? $data->created_at->format('Y-m-d H:i:s') : '',
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
