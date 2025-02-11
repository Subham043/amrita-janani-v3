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

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function ImageModel()
    {
        return $this->belongsTo(ImageModel::class, 'image_id');
    }
}
