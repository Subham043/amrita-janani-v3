<?php

namespace App\Modules\Videos\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoFavourite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "video_favourites";

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function VideoModel()
    {
        return $this->belongsTo(VideoModel::class, 'video_id');
    }
}
