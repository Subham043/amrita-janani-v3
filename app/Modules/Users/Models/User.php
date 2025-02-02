<?php

namespace App\Modules\Users\Models;

use App\Enums\Restricted;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Modules\Users\Notifications\ResetPasswordQueued;
use App\Modules\Users\Notifications\VerifyEmailQueued;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_type',
        'is_social',
        'status',
        'password',
        'dark_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'user_type' => UserType::User,
        'status' => UserStatus::Active,
        'is_social' => Restricted::No,
        'phone' => null
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    protected function status(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $value == "on" ? UserStatus::Active->value() : UserStatus::Blocked->value(),
        );
    }

    public function getPassword(){
        return $this->password;
    }

    public function ImageModel()
    {
        return $this->hasMany('App\Models\ImageModel', 'user_id');
    }

    public function DocumentModel()
    {
        return $this->hasMany('App\Models\DocumentModel', 'user_id');
    }

    public function AudioModel()
    {
        return $this->hasMany('App\Models\AudioModel', 'user_id');
    }

    public function VideoModel()
    {
        return $this->hasMany('App\Models\VideoModel', 'user_id');
    }

    public function ImageFavourite()
    {
        return $this->hasMany('App\Models\ImageFavourite', 'image_id');
    }

    public function DocumentFavourite()
    {
        return $this->hasMany('App\Models\DocumentFavourite', 'document_id');
    }

    public function AudioFavourite()
    {
        return $this->hasMany('App\Models\AudioFavourite', 'audio_id');
    }

    public function VideoFavourite()
    {
        return $this->hasMany('App\Models\VideoFavourite', 'video_id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailQueued);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordQueued($token));
    }

    /**
     * User Factory.
     *
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }
    
    public function hasCompletedAccountSetup(): bool
    {
        return !is_null($this->password) && $this->is_social == Restricted::No->value();
    }
}
