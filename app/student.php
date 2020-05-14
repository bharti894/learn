<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens; 

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\student as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyApiEmail;


class student extends Authenticatable implements MustVerifyEmail
{


    use Notifiable, HasApiTokens;

    protected $guard = 'student';

    protected $fillable = [
        'fname', 'lname', 'fathersname','dateofbirth','image','email','password','country','city','mobileno'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    
    ];    
    
    public function sendApiEmailVerificationNotification()
    {
    $this->notify(new VerifyApiEmail); // my notification
    }
    
    public function user()
    {
    //return $this->hasOne(User::class,'student_id');
    return $this->hasMany(User::class,'student_id','id'); 
    //return $this->belongsTo(User::class,'student_id','id'); 
    //Here is student_id is foreign key in table users and it is optional but ensure that its name student match with model name
    //id is local key of table student
    }
    
}

