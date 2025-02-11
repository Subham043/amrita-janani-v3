<?php

namespace App\Modules\Images\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
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

    protected $appends = ['image_link', 'image_compressed_link', 'tags_array', 'topics_array'];

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
    
    protected function imageCompressedLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->image) && Storage::exists($this->file_path.'compressed-'.$this->image)) ? URL::temporarySignedRoute(
                'image_file',
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

    public function file_format(){
        return File::extension($this->image);
    }

    public function time_elapsed(){

        $dt = Carbon::parse($this->created_at);
        return $dt->diffForHumans();

    }

    public function contentVisible(){

        if($this->restricted==0 || Auth::user()->user_type!=2){
            return true;
        }else{
            try {
                $imageAccess = ImageAccess::where('image_id', $this->id)->where('user_id', Auth::user()->id)->first();
            } catch (\Throwable $th) {
                //throw $th;
                $imageAccess = null;
            }

            if(empty($imageAccess) || $imageAccess->status==0){
                return false;
            }else{
                return true;
            }
        }
    }

    public function markedFavorite(){
        try {
            $imageFav = ImageFavourite::where('image_id', $this->id)->where('user_id', Auth::user()->id)->first();
        } catch (\Throwable $th) {
            //throw $th;
            $imageFav = null;
        }
        if(!empty($imageFav)){
            if($imageFav->status == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }

    public function getTagsArray() {
        if($this->tags){
            $arr = explode(",",$this->tags);
            return $arr;
        }
        return array();
    }

}
