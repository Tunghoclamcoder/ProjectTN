<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;
    protected $guard_name = 'customer';

    protected $fillable = [
        'customer_name',
        'email',
        'password',
        'phone_number',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
