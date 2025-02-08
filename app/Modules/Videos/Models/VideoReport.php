<?php

namespace App\Modules\Videos\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoReport extends Model
{
    use HasFactory;

    protected $table = "video_reports";

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function Admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public function VideoModel()
    {
        return $this->belongsTo(VideoModel::class, 'video_id');
    }
}
