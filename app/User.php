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

    public static function findByEmail($email)
    {
        // static es equivalente a User ya que estamos en la clase user
        return static::whereEmail($email)->first();
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault([
            'bio' => 'Programador',
        ]);
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return;
        }

        $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    }

}
