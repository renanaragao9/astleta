<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'username',
        'date',
        'gender',
        'cpf',
        'email',
        'phone',
        'password',
        'qtd_login',
        'last_login',
        'type',
        'lang',
        'email_verified_at',
        'phone_verified_at',
        'provider',
        'provider_id',
        'asaas_customer_id',
        'image_path',
        'is_active',
        'is_public',
        'profile_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'date' => 'date',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'qtd_login' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function teamPlayers(): HasMany
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function emailVerificationCodes(): HasMany
    {
        return $this->hasMany(EmailVerificationCode::class);
    }

    public function latestEmailVerificationCode(): HasOne
    {
        return $this->hasOne(EmailVerificationCode::class)->latestOfMany();
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        // Esta implementação será personalizada via Events/Jobs
        // O Laravel usa este método por padrão, mas vamos usar nosso sistema customizado
    }

    public function tabs(): HasMany
    {
        return $this->hasMany(Tab::class);
    }

    public function notificationMessages(): HasMany
    {
        return $this->hasMany(NotificationMessage::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function athleteProfile(): HasOne
    {
        return $this->hasOne(AthleteProfile::class);
    }

    public function createNotificationMessage($type, $data): void
    {
        $this->notificationMessages()->create([
            'type' => $type,
            'data' => $data,
        ]);
    }
}
