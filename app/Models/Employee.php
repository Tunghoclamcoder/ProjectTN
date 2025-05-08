<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $table = 'employee';

    protected $primaryKey = 'employee_id';

    protected $guard_name = 'employee';

    protected $fillable = [
        'employee_name',
        'email',
        'password',
        'phone_number',
        'status',
        'owner_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
