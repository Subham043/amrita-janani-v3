<?php

namespace App\Modules\Audios\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioAccess extends Model
{
    use HasFactory;

    protected $table = "audio_access";

    protected $fillable = [
        'audio_id',
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
    
    public function AudioModel()
    {
        return $this->belongsTo(AudioModel::class, 'audio_id')->withDefault();
    }
}
