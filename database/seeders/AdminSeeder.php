<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $data = [

            'name' => 'Admin',
            'user_name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'remember_token' => bcrypt('admin_token')
        ];
        \App\Models\Admin::create($data);

    }
}
