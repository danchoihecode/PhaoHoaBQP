<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'first_name' => 'Ta',
            'last_name' => 'Van Hoan',
            'phone' => '1234567890',
            'email' => 'hoanjp197@gmail.com',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'status' => 1,
        ]);
    }
}
