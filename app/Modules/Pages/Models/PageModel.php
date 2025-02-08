<?php

namespace App\Modules\Pages\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="pages";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'page_name',
        'url',
        'user_id',
    ];


    public function User()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function PageContentModel()
    {
        return $this->hasMany(PageContentModel::class, 'page_id');
    }

}
