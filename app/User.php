<?php

namespace App;

use App\UserProfile;
use Illuminate\Database\Eloquent\Builder;
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
        'active' => 'bool',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new UserQuery($query);
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

    public function setStateAttribute($value)
    {
        $this->attributes['active'] = ($value == 'active') ;
    }

    public function getStateAttribute()
    {
        // por esto ya no sale si esta inactivo, ya que el state se pone a null.
        if($this->active != null){
            return $this->active ? 'active' : 'inactive';
        }
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

}
