<?php

namespace App\Modules\Languages\Models;

use App\Modules\Audios\Models\AudioModel;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Users\Models\User;
use App\Modules\Videos\Models\VideoModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguageModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="languages";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    public function User()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function Audios()
    {
        return $this->belongsToMany(AudioModel::class, 'audio_languages', 'audio_id', 'language_id');
    }

    public function Videos()
    {
        return $this->belongsToMany(VideoModel::class, 'video_languages', 'video_id', 'language_id');
    }

    public function Documents()
    {
        return $this->belongsToMany(DocumentModel::class, 'document_languages', 'document_id', 'language_id');
    }


}
