<?php

namespace App\Modules\Users\Models;

use App\Enums\Restricted;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Modules\Audios\Models\AudioFavourite;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Documents\Models\DocumentFavourite;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Images\Models\ImageFavourite;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Users\Notifications\ResetPasswordQueued;
use App\Modules\Users\Notifications\VerifyEmailQueued;
use App\Modules\Videos\Models\VideoFavourite;
use App\Modules\Videos\Models\VideoModel;
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
        'phone' => null,
    ];

    protected $appends = ['role'];

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

    protected function role(): Attribute
    {
        return new Attribute(
            get: fn () => $this->isAdmin() ? "Admin" : ($this->user_type == UserType::User->value() ? "User" : "Previledge User"),
        );
    }

    public function getPassword(){
        return $this->password;
    }

    public function ImageModel()
    {
        return $this->hasMany(ImageModel::class, 'user_id');
    }

    public function DocumentModel()
    {
        return $this->hasMany(DocumentModel::class, 'user_id');
    }

    public function AudioModel()
    {
        return $this->hasMany(AudioModel::class, 'user_id');
    }

    public function VideoModel()
    {
        return $this->hasMany(VideoModel::class, 'user_id');
    }

    public function ImageFavourite()
    {
        return $this->hasMany(ImageFavourite::class, 'image_id');
    }

    public function DocumentFavourite()
    {
        return $this->hasMany(DocumentFavourite::class, 'document_id');
    }

    public function AudioFavourite()
    {
        return $this->hasMany(AudioFavourite::class, 'audio_id');
    }

    public function VideoFavourite()
    {
        return $this->hasMany(VideoFavourite::class, 'video_id');
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
    
    public function isUser(): bool
    {
        return $this->user_type != UserType::Admin->value();
    }
    
    public function isAdmin(): bool
    {
        return $this->user_type == UserType::Admin->value();
    }
    
    public function isNotBlocked(): bool
    {
        return $this->status == UserStatus::Active->value();
    }
}
