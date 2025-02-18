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
        return PageModel::where('url', '!=', 'home')->where('url', '!=', 'about');
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->defaultSort('-id')
            ->allowedSorts('id')
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonPageFilter, null, false),
            ]);
    }

    public function slug($slug): PageModel
    {
        return $this->model()->where('url', $slug)->firstOrFail();
    }
    
    public function getHomePage(): PageModel
    {
        return PageModel::where('url', 'home')->firstOrFail();
    }
    
    public function getAboutPage(): PageModel
    {
        return PageModel::where('url', 'about')->firstOrFail();
    }

    public function excel(): SimpleExcelWriter
    {
        $model = $this->query();
        $i = 0;
        $writer = SimpleExcelWriter::streamDownload('pages.xlsx');
        foreach ($model->lazy(1000)->collect() as $data) {
            $writer->addRow([
                'Id' => $data->id,
                'Title' => $data->title,
                'Page Name' => $data->page_name,
                'URL' => $data->url,
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

class CommonPageFilter implements Filter
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
