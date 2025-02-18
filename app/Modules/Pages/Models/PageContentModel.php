<?php

namespace App\Modules\Pages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageContentModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="page_contents";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'heading',
        'image',
        'image_position',
        'description',
        'description_unformatted',
        'page_id',
    ];

    protected $appends = ['image_link'];

    public $file_path = 'upload/pages/';
    
    protected function imageLink(): Attribute
    {
        return new Attribute(
            get: fn () => is_null($this->image) ? null : asset('storage/'.$this->file_path.$this->image),
        );
    }
    
    protected function imageCompressedLink(): Attribute
    {
        return new Attribute(
            get: fn () => is_null($this->image) ? null : asset('storage/'.$this->file_path.'compressed-'.$this->image),
        );
    }


    public function PageModel()
    {
        return $this->belongsTo(PageModel::class, 'page_id')->withDefault();
    }

}
