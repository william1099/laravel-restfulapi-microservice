<?php

namespace App;

use App\Traits\ScopeAdmin;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;
    use ScopeAdmin; 

    const REGULAR_USER = "false";
    const ADMIN_USER = "true";

    const VERIFIED_USER = "true";
    const UNVERIFIED_USER = "false";

    protected $table = "users";
    protected $dates = [
        "deleted_at"
    ];

    protected static function boot() {
        parent::boot();

        static::observe(new \App\Observer\UserObserver);
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', "verified", "admin", "verification_token", "gender"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    //, "verification_token"

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isVerified() {
        return $this->verified == User::VERIFIED_USER;
    }

    public function isAdmin() {
        return $this->admin == User::ADMIN_USER;
    }

    public static function generateVerificationToken() {
        return Str::random(40);
    }

    public function setPasswordAttribute($password) {

        $this->attributes["password"] = Hash::make($password);

    }

    public function getNameAttribute($name) {
        
        return ucwords($name);
    }

    public function setNameAttribute($name) {

        $this->attributes["name"] = strtolower($name);
        
    }

    public function setEmailAttribute($email) {

        $this->attributes["email"] = strtolower($email);
        
    }

    public function hasAttribute($attr) {
        return array_key_exists($attr, $this->attributes);
    }
    
}
