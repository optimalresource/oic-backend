<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AppInfoTableSeeder;
use Database\Seeders\RoleTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleTableSeeder::class,
            AppInfoTableSeeder::class,
        ]);

        // \App\Models\User::factory(5)->create();
    }
}
