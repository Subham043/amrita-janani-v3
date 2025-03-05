<?php

namespace App\Modules\Videos\Models;

use App\Enums\UserType;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Videos\Models\VideoAccess;
use App\Modules\Videos\Models\VideoFavourite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;

class VideoModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="videos";

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
        'language_id',
        'deity',
        'views',
        'favourites',
        'video',
        'topics',
        'status',
        'restricted',
        'user_id',
    ];

    protected $attributes = [
        'status' => 1,
        'restricted' => 0,
    ];

    protected $appends = ['tags_array', 'topics_array'];

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

    public function Languages()
    {
        return $this->belongsToMany(LanguageModel::class, 'video_languages', 'video_id', 'language_id');
    }

    public function GetLanguagesId(){
        return $this->Languages()->pluck('languages.id')->toArray();
    }

    public function GetLanguagesName(){
        return $this->Languages()->pluck('languages.name');
    }

    public function VideoFavourite()
    {
        return $this->hasMany(VideoFavourite::class, 'video_id');
    }

    public function VideoAccess()
    {
        return $this->hasMany(VideoAccess::class, 'video_id');
    }

    public function VideoReport()
    {
        return $this->hasMany(VideoReport::class, 'video_id');
    }

    public function CurrentUserReported()
    {
        return $this->hasOne(VideoReport::class, 'video_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }
    
    public function CurrentUserAccessible()
    {
        return $this->hasOne(VideoAccess::class, 'video_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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
        return $this->hasOne(VideoFavourite::class, 'video_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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

    public function getVideoId(){
        if(strpos($this->video,'vimeo') !== false){
            if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/?(showcase\/)*([0-9))([a-z]*\/)*([0-9]{6,11})[?]?.*/", $this->video, $output_array)) {
                return $output_array[6];
            }
        }else{
            $video_id = explode("/embed/", $this->video);
            $video_id = $video_id[1];
            return $video_id;
        }
    }
}
