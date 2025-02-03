<?php

namespace App\Modules\Languages\Services;

use App\Abstracts\AbstractExcelService;
use App\Modules\Languages\Models\LanguageModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;

class LanguageService extends AbstractExcelService
{
    public function model(): Builder
    {
        return LanguageModel::query();
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
                'Name' => $data->name,
                'Status' => $data->status==1 ? 'Active' : 'Inactive',
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
            $q->where('name', 'LIKE', '%' . $value . '%');
        });
    }
}
