<?php

namespace App\Abstracts;

use App\Interfaces\AuthServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

abstract class AbstractAuthenticableService implements AuthServiceInterface
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

	public function getById(Int $id): User
	{
		return $this->model()->findOrFail($id);
	}

	public function getByEmail(String $email): User
	{
		return $this->model()->where('email', $email)->firstOrFail();
	}

	public function getByPhone(String $phone): User
	{
		return $this->model()->where('phone', $phone)->firstOrFail();
	}

	public function create(array $data): User
	{
		return $this->model()->create([...$data, 'otp' => rand(1111, 9999)]);
	}

	public function update(array $data, $user): User
	{
		$user->update($data);
		$user->refresh();
		return $user;
	}

	public function syncRoles(array $roles = [], $user): void
	{
		$user->syncRoles($roles);
	}

	public function delete($user): bool
	{
		return $user->delete();
	}
}
