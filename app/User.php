<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
    'is_admin' => 'boolean',
    ];

    protected $guarded = ['is_admin'];

    public static function findByEmail($email)
    {
                // static es equivalente a User ya que estamos en la clase user
        return static::where(compact('email'))->first();
    }

    public function isAdmin()
    {
        return $this->email === 'adri@gmail.com';
    }
}
