<?php

namespace App\Modules\Images\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageFavourite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "image_favourites";

    protected $fillable = [
        'user_id', 'image_id', 'status'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    
    public function ImageModel()
    {
        return $this->belongsTo(ImageModel::class, 'image_id')->withDefault();
    }
}
