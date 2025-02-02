<?php

namespace App\Modules\Documents\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Modules\Documents\Models\DocumentAccess;
use App\Modules\Documents\Models\DocumentFavourite;
use App\Modules\Languages\Models\LanguageModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DocumentModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="documents";

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
        'page_number',
        'language_id',
        'deity',
        'views',
        'favourites',
        'document',
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

    public function Languages()
    {
        return $this->belongsToMany(LanguageModel::class, 'document_languages', 'document_id', 'language_id');
    }

    public function GetLanguagesId(){
        return $this->Languages()->pluck('languages.id')->toArray();
    }

    public function GetLanguagesName(){
        return $this->Languages()->pluck('languages.name');
    }

    public function DocumentFavourite()
    {
        return $this->hasMany('App\Models\DocumentFavourite', 'document_id');
    }

    public function DocumentAccess()
    {
        return $this->hasMany('App\Models\DocumentAccess', 'document_id');
    }

    public function DocumentReport()
    {
        return $this->hasMany('App\Models\DocumentReport', 'document_id');
    }

    public function file_format(){
        return File::extension($this->document);
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
                $documentAccess = DocumentAccess::where('document_id', $this->id)->where('user_id', Auth::user()->id)->first();
            } catch (\Throwable $th) {
                throw $th;
                $documentAccess = null;
            }

            if(empty($documentAccess) || $documentAccess->status==0){
                return false;
            }else{
                return true;
            }
        }
    }

    public function markedFavorite(){
        try {
            $documentFav = DocumentFavourite::where('document_id', $this->id)->where('user_id', Auth::user()->id)->first();
        } catch (\Throwable $th) {
            //throw $th;
            $documentFav = null;
        }
        if(!empty($documentFav)){
            if($documentFav->status == 1){
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
