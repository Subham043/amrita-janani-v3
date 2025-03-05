<?php

namespace App\Modules\Images\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Images\Models\ImageAccess;
use App\Modules\Images\Models\ImageFavourite;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ImageModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="images";

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
        'image',
        'topics',
        'status',
        'restricted',
        'user_id',
    ];

    protected $attributes = [
        'status' => 1,
        'restricted' => 0,
    ];

    protected $appends = ['image_link', 'content_image_link', 'content_image_thumbnail_link', 'tags_array', 'topics_array'];

    public $file_path = 'upload/images/';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) str()->uuid();
        });
    }

    protected function imageLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->image) && Storage::exists($this->file_path.$this->image)) ? URL::temporarySignedRoute(
                'image_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid, 'compressed' => false]
            ) : null,
        );
    }
    
    protected function contentImageLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->image) && Storage::exists($this->file_path.$this->image)) ? URL::temporarySignedRoute(
                'content_image_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid, 'compressed' => false]
            ) : null,
        );
    }
    
    protected function contentImageThumbnailLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->image) && Storage::exists($this->file_path.$this->image)) ? URL::temporarySignedRoute(
                'content_image_thumbnail_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid, 'compressed' => true]
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

    public function ImageFavourite()
    {
        return $this->hasMany(ImageFavourite::class, 'image_id');
    }

    public function ImageAccess()
    {
        return $this->hasMany(ImageAccess::class, 'image_id');
    }

    public function ImageReport()
    {
        return $this->hasMany(ImageReport::class, 'image_id');
    }

    public function CurrentUserReported()
    {
        return $this->hasOne(ImageReport::class, 'image_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }
    
    public function CurrentUserAccessible()
    {
        return $this->hasOne(ImageAccess::class, 'image_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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
        return $this->hasOne(ImageFavourite::class, 'image_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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
        return File::extension($this->image);
    }

}
