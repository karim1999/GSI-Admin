<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'middleName', 'lastName', 'email', 'password', 'type', 'phone', 'civilIDNumber', 'gender',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function course(){
        return $this->hasMany('App\Courses');
    }

    public function jointCourses(){
        return $this->belongsToMany('App\Courses','joint_courses');
    }

    public function lecture(){
        return $this->hasMany('App\Lectures');
    }

    public function jointLectures(){
        return $this->belongsToMany('App\Lectures', 'joint_lectures')->withPivot(['amount']);
    }

    public function comments(){
        return $this->hasMany('App\Comments');
    }

    public function wallet(){
        return $this->hasOne('App\Wallet');
    }
}
