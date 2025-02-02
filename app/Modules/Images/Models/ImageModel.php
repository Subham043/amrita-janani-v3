<?php

namespace App\Modules\Images\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Modules\Images\Models\ImageAccess;
use App\Modules\Images\Models\ImageFavourite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) str()->uuid();
        });
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $value == "on" ? 1 : 0,
        );
    }

    protected function restricted(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $value == "on" ? 1 : 0,
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
        return $this->belongsTo('App\Models\User')->withDefault();
    }

    public function getAdminName(){
        if(!empty($this->User) && $this->User->count()>0){
            return $this->User->name;
        }
        return "";
    }

    public function ImageFavourite()
    {
        return $this->hasMany('App\Models\ImageFavourite', 'image_id');
    }

    public function ImageAccess()
    {
        return $this->hasMany('App\Models\ImageAccess', 'image_id');
    }

    public function ImageReport()
    {
        return $this->hasMany('App\Models\ImageReport', 'image_id');
    }

    public function file_format(){
        return File::extension($this->image);
    }

    public function time_elapsed(){

        $dt = Carbon::parse($this->created_at);
        return $dt->diffForHumans();

    }

    public function contentVisible(){

        if($this->restricted==0 || Auth::user()->userType!=2){
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
