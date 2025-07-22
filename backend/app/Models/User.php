<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyEmailTrait;
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'active',
        'birthdate',
        'last_birthday_greeted_at'
    ];
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom());
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_user');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_opt_out' => 'boolean',
        ];
    }
}   
