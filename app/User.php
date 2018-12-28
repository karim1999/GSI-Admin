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

    public static function send($tokens,$text)
    {
        $fields = array
        (
            "registration_ids" => $tokens,
            "priority" => 10,
            'data' => [
                'text' => $text,
            ],
            'notification' => [
                'text' => $text,
            ],
            'vibrate' => 1,
            'sound' => 1
        );
        $headers = array
        (
            'accept: application/json',
            'Content-Type: application/json',
            'Authorization:key=
            AAAAy7-Sztw:APA91bGMdCL_Gapq_S7B_bGYkaEwRJoRqZWBhSe9iquQ569cVlqC-hWLT2FWgnhhQho2mZRT1L9SUnK5gg5wXwksefaS1BbDdQQFCFWWxLOI66xRbroSd4Hin0b1DrmjggADVqJDgxPk'

        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //  var_dump($result);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
}
