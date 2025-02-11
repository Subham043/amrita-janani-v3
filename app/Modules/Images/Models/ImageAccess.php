<?php

namespace App\Modules\Images\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageAccess extends Model
{
    use HasFactory;

    protected $table = "image_access";

    protected $fillable = [
        'image_id',
        'user_id',
        'status',
        'message'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function Admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public function ImageModel()
    {
        return $this->belongsTo(ImageModel::class, 'image_id');
    }
}
