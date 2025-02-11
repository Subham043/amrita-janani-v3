<?php

namespace App\Modules\Documents\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAccess extends Model
{
    use HasFactory;

    protected $table = "document_access";

    protected $fillable = [
        'document_id',
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
    
    public function DocumentModel()
    {
        return $this->belongsTo(DocumentModel::class, 'document_id');
    }
}
