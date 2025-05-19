<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class OwnerSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('owner')->count() === 0) {
            DB::table('owner')->insert([
                'owner_name' => 'Store Owner',
                'email' => 'owner@gmail.com',
                'phone_number' => '0123456789',
                'password' => Hash::make('admin123'),
            ]);
        }
    }
}
