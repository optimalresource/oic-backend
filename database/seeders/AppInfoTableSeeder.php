<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AppInfo;

class AppInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppInfo::create([
            'name' => 'Oasis International Conference',
            'phone1' => '09029292929',
        ]);
    }
}
