<?php

namespace App\Modules\Banners\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BannerModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="banners";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'user_id'
    ];

    protected $appends = ['image_link'];

    public $file_path = 'upload/banners/';
    
    protected function imageLink(): Attribute
    {
        return new Attribute(
            get: fn () => is_null($this->image) ? null : asset('storage/'.$this->file_path.$this->image),
        );
    }

    public function User()
    {
        return $this->belongsTo(User::class)->withDefault();
    }


}
