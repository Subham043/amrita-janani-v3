<?php

namespace App\Modules\Audios\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Audios\Models\AudioAccess;
use App\Modules\Audios\Models\AudioFavourite;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AudioModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="audios";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'uuid',
        'description',
        'description_unformatted',
        'tags',
        'year',
        'version',
        'duration',
        'language_id',
        'deity',
        'views',
        'favourites',
        'audio',
        'topics',
        'status',
        'restricted',
        'user_id',
    ];

    protected $attributes = [
        'status' => 1,
        'restricted' => 0,
    ];

    protected $appends = ['audio_link', 'content_audio_link', 'tags_array', 'topics_array'];

    public $file_path = 'upload/audios/';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) str()->uuid();
        });
    }

    protected function audioLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->audio) && Storage::exists($this->file_path.$this->audio)) ? URL::temporarySignedRoute(
                'audio_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid]
            ) : null,
        );
    }

    protected function contentAudioLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->audio) && Storage::exists($this->file_path.$this->audio)) ? URL::temporarySignedRoute(
                'content_audio_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid]
            ) : null,
        );
    }

    protected function tagsArray(): Attribute
    {
        return new Attribute(
            get: fn () => $this->tags ? explode(",",$this->tags) : array(),
        );
    }

    protected function topicsArray(): Attribute
    {
        return new Attribute(
            get: fn () => $this->topics ? explode(",",$this->topics) : array(),
        );
    }

    protected function userId(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Auth::user()->id,
        );
    }

    public function User()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function getAdminName(){
        if(!empty($this->User) && $this->User->count()>0){
            return $this->User->name;
        }
        return "";
    }

    public function AudioFavourite()
    {
        return $this->hasMany(AudioFavourite::class, 'audio_id');
    }

    public function AudioAccess()
    {
        return $this->hasMany(AudioAccess::class, 'audio_id');
    }

    public function AudioReport()
    {
        return $this->hasMany(AudioReport::class, 'audio_id');
    }

    public function Languages()
    {
        return $this->belongsToMany(LanguageModel::class, 'audio_languages', 'audio_id', 'language_id');
    }

    public function GetLanguagesId(){
        return $this->Languages()->pluck('languages.id')->toArray();
    }

    public function GetLanguagesName(){
        return $this->Languages()->pluck('languages.name');
    }

    public function CurrentUserReported()
    {
        return $this->hasOne(AudioReport::class, 'audio_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }
    
    public function CurrentUserAccessible()
    {
        return $this->hasOne(AudioAccess::class, 'audio_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }

    public function contentVisible(){

        if($this->restricted==0 || Auth::user()->user_type!=UserType::User->value()){
            return true;
        }else{
            if(empty($this->CurrentUserAccessible) || $this->CurrentUserAccessible->status==0){
                return false;
            }else{
                return true;
            }
        }
    }

    public function CurrentUserFavourite()
    {
        return $this->hasOne(AudioFavourite::class, 'audio_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }

    public function markedFavorite(){
        if(!empty($this->CurrentUserFavourite)){
            if($this->CurrentUserFavourite->status == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function file_format(){
        return File::extension($this->audio);
    }

}
