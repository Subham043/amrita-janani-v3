<?php

namespace App\Modules\Videos\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoAccess extends Model
{
    use HasFactory;

    protected $table = "video_access";

    protected $fillable = [
        'video_id',
        'user_id',
        'status',
        'message'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    
    public function Admin()
    {
        return $this->belongsTo(User::class, 'admin_id')->withDefault();
    }
    
    public function VideoModel()
    {
        return $this->belongsTo(VideoModel::class, 'video_id')->withDefault();
    }
}
