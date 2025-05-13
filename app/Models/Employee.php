<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $table = 'employee';
    protected $primaryKey = 'employee_id';
    public $timestamps = false;
    protected $fillable = [
        'employee_name',
        'email',
        'phone_number',
        'password',
        'status',
        'owner_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'status' => 'string',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'owner_id');
    }
}
