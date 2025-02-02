<?php
namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
	public function model(): Builder;
	public function login(array $credentials, string $guard): bool;
	public function generate_token($user): string;
	public function profile(string $guard): User;
	public function logout(Request $request, string $guard): void;
	public function getById(Int $id): User;
	public function getByEmail(String $email): User;
	public function getByPhone(String $phone): User;
	public function update(array $data, $user): User;
	public function setPasswordResetLink(array $data): void;
	public function deletePasswordResetLink(string $uuid): void;
	public function getPasswordResetLink(string $uuid): Model|null;
}