<?php

namespace App\Modules\Audios\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AudioFavourite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "audio_favourites";

    protected $fillable = [
        'user_id', 'audio_id', 'status'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    
    public function AudioModel()
    {
        return $this->belongsTo(AudioModel::class, 'audio_id')->withDefault();
    }
}
