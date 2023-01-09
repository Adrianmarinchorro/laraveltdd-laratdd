<?php

namespace App;

use App\UserProfile;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
    //
    ];

    protected $perPage = 15;

    public function getPerPage()
    {
       return parent::getPerPage() * 2; // 15 * 2
    }

    public static function findByEmail($email)
    {
                // static es equivalente a User ya que estamos en la clase user
        return static::whereEmail($email)->first();
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault([
            'bio' => 'Programador',
        ]);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
