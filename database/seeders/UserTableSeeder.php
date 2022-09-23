<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Pastor Seiya',
            'email' => 'seiya@gmail.com',
            'password' => 'light20',
            'role_id' => 1
        ]);
    }
}
