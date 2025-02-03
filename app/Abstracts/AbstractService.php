<?php

namespace App\Abstracts;

use App\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

abstract class AbstractService implements ServiceInterface
{
	abstract public function model(): Builder;
	abstract public function query(): QueryBuilder;

	public function all(): Collection
	{
		return $this->query()->lazy(100)->collect();
	}

	public function paginate(Int $total = 10): LengthAwarePaginator
	{
		return $this->query()
			->paginate($total)
			->appends(request()->query());
	}

	public function getById(Int $id): Model
	{
		return $this->model()->findOrFail($id);
	}

	public function create(array $data): Model
	{
		return $this->model()->create($data);
	}

	public function update(array $data, $model): Model
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
}
