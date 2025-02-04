<?php

namespace App\Modules\Pages\Services;

use App\Abstracts\AbstractExcelService;
use App\Modules\Pages\Models\PageModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;

class PageService extends AbstractExcelService
{
    public function model(): Builder
    {
        return PageModel::query();
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

    public function excel(): SimpleExcelWriter
    {
        $model = $this->query();
        $i = 0;
        $writer = SimpleExcelWriter::streamDownload('users.xlsx');
        foreach ($model->lazy(1000)->collect() as $data) {
            $writer->addRow([
                'Id' => $data->id,
                'Title' => $data->title,
                'Page Name' => $data->page_name,
                'URL' => $data->url,
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
            $q->where('title', 'LIKE', '%' . $value . '%')
            ->orWhere('page_name', 'LIKE', '%' . $value . '%')
            ->orWhere('url', 'LIKE', '%' . $value . '%');
        });
    }
}
