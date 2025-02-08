<?php

namespace App\Modules\Images\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageReport extends Model
{
    use HasFactory;

    protected $table = "image_reports";

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
