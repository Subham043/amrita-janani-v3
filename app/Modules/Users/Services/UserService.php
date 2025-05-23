<?php

namespace App\Modules\Users\Services;

use App\Abstracts\AbstractExcelService;
use App\Modules\Users\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\SimpleExcel\SimpleExcelWriter;

class UserService extends AbstractExcelService
{
    public function model(): Builder
    {
        return User::query();
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
                AllowedFilter::callback('verification', function (Builder $query, $value) {
                    if($value == 'yes') {
                        $query->whereNotNull('email_verified_at');
                    }elseif($value == 'no') {
                        $query->whereNull('email_verified_at');
                    }
                }),
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
                'Email' => $data->email,
                'Phone' => $data->phone,
                'Role' => $data->role,
                'Status' => $data->status==1 ? 'Active' : 'Blocked',
                'Verification' => $data->email_verified_at ? 'Verified' : 'Not Verified',
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
            $q->where('name', 'LIKE', '%' . $value . '%')
            ->orWhere('email', 'LIKE', '%' . $value . '%')
            ->orWhere('phone', 'LIKE', '%' . $value . '%');
        });
    }
}
