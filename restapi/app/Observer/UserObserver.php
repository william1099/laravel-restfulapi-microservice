<?php 

namespace App\Observer;

use App\User;
use Illuminate\Support\Facades\Mail;

class UserObserver {
    public function created($user) {

        Mail::to($user->email)->send(new \App\Mail\UserCreated($user));

    }

    public function updated($user) {

        if($user->isDirty("email")) {
            Mail::to($user->email)->send(new \App\Mail\UserEmailChanged($user));
        }
        
    }
}