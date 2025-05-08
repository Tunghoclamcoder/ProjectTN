<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Owner extends Authenticatable
{
    use Notifiable;

    protected $table = 'owner';
    protected $primaryKey = 'owner_id';

    protected $fillable = ['owner_name', 'email', 'password', 'phone_number'];

    protected $hidden = ['password', 'remember_token'];
}
