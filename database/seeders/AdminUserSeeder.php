<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = (new User())->fill([
            'name'              => 'Super Admin',
            'email'             => 'admin@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make('12345678'),
            'phone'             => '01700000000',
            'gender'            => User::MALE,
            'is_system'         => '1',
            'is_active'         => '1',
            'default_language'  => 'en',
        ]);

        $user->save();

        $user->assignRole('Admin');
    }
}
