<?php

namespace App\Modules\Documents\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLanguage extends Model
{
    use HasFactory;
    protected $table="document_languages";
    protected $guarded=['id'];
}
