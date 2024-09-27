<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultRoleSeeder::class);
        $this->call(AdminUserSeeder::class);
//        $this->call(CreateUserSeeder::class);
        $this->call(CreateCategorySeeder::class);
//        $this->call(CreateTicketSeeder::class);
//        $this->call(CreateTicketReplaySeeder::class);
//        $this->call(CreateFAQsSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(AddRegionCodeInSettingsSeeder::class);
    }
}
