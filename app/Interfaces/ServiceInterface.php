<?php
namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface ServiceInterface
{
	public function model(): Builder;
	public function query(): QueryBuilder;
	public function all(): Collection;
	public function paginate(): LengthAwarePaginator;
	public function getById(Int $id): Model;
	public function create(array $data): Model;
	public function update(array $data, $model): Model;
	public function delete($model): bool;
	public function forceDelete($model): bool;
}