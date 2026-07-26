<?php

namespace App\Models\Fianut;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'password',
        'token',
        'is_owner',
        'instance_code',
        'nickname',
        'active',
        'gender',
        'referral_code',
        'address',
        'image',
        'sallary',
        'target_per_month',
        'poins',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
protected $hidden = [
         'password',
         'remember_token',
         'token',
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

    public function instance()
    {
        return $this->belongsTo(Instances::class, 'instance_code', 'instance_code');
    }

    public function userPriviledges()
    {
        return $this->hasMany(UserPriviledges::class, 'user_id', 'id');
    }

}
