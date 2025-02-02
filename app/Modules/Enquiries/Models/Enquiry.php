<?php

namespace App\Modules\Enquiries\Models;

use App\Events\EnquirySubmitted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            event(new EnquirySubmitted($model));
        });
    }

    protected function systemInfo(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => is_null($value) ? null : json_decode($value, true),
        );
    }
}
