<?php

namespace App\Modules\Documents\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentFavourite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "document_favourites";

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function DocumentModel()
    {
        return $this->belongsTo(DocumentModel::class, 'document_id');
    }
}
