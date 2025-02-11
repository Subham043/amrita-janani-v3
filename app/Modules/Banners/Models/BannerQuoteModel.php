<?php

namespace App\Modules\Banners\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerQuoteModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="banner_quotes";

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote',
        'user_id'
    ];

    public function User()
    {
        return $this->belongsTo(User::class)->withDefault();
    }


}
