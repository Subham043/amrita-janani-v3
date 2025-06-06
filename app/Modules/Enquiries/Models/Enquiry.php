<?php

namespace App\Modules\Enquiries\Models;

use App\Events\EnquirySubmitted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="enquiries";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'ip_address',
        'system_info',
        'subject',
        'message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'system_info' => 'array',
        ];
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            event(new EnquirySubmitted($model));
        });
    }
}
