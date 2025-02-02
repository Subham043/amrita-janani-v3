<?php

namespace App\Modules\Banners\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="banners";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'user_id'
    ];

    public function User()
    {
        return $this->belongsTo('App\Models\User')->withDefault();
    }


}
