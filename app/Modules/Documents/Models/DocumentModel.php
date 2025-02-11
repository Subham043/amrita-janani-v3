<?php

namespace App\Modules\Documents\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Documents\Models\DocumentAccess;
use App\Modules\Documents\Models\DocumentFavourite;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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

    protected $appends = ['document_link', 'content_document_link', 'tags_array', 'topics_array'];

    public $file_path = 'upload/documents/';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) str()->uuid();
        });
    }

    protected function documentLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->document) && Storage::exists($this->file_path.$this->document)) ? URL::temporarySignedRoute(
                'document_file',
                now()->addMinutes(5),
                ['uuid' => $this->uuid]
            ) : null,
        );
    }
    
    protected function contentDocumentLink(): Attribute
    {
        return new Attribute(
            get: fn () => (!is_null($this->document) && Storage::exists($this->file_path.$this->document)) ? URL::temporarySignedRoute(
                'content_document_file',
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
        return $this->hasMany(DocumentFavourite::class, 'document_id');
    }

    public function DocumentAccess()
    {
        return $this->hasMany(DocumentAccess::class, 'document_id');
    }

    public function DocumentReport()
    {
        return $this->hasMany(DocumentReport::class, 'document_id');
    }

    public function CurrentUserReported()
    {
        return $this->hasOne(DocumentReport::class, 'document_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
    }
    
    public function CurrentUserAccessible()
    {
        return $this->hasOne(DocumentAccess::class, 'document_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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
        return $this->hasOne(DocumentFavourite::class, 'document_id')->where('user_id', Auth::user()->id)->latestOfMany('id', 'desc');
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
        return File::extension($this->document);
    }

}
