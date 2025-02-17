<?php

namespace App\Modules\SearchHistories\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="search_histories";

    protected $fillable = [
        'search',
        'user_id',
        'screen',
    ]; 

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

}
