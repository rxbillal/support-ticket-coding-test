<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRepo = App::make(UserRepository::class);
        $faker = Faker::create();
        $agentRole = Role::whereName('Agent')->first();

        $agent = [
            'name' => 'Mr. Agent',
            'email' => 'agent@gmail.com',
            'phone' => '01900000000',
            'email_verified_at' => Carbon::now(),
            'password' => '12345678',
            'is_system' => 0,
            'is_active' => 1,
            'gender' => User::MALE,
            'default_language'  => 'en',
        ];
        $agent['role'] = $agentRole->id;
        $userRepo->store($agent);

        foreach (range(1, 10) as $index) {
            try {
                $input = [
                    'name'      => $faker->name,
                    'email'     => $faker->unique()->safeEmail,
                    'phone'     => $faker->numerify('##########'),
                    'password'  => '123456',
                    'role'      => $agentRole->id,
                    'is_system' => 0,
                    'is_active' => 1,
                    'gender' => rand(1, 2),
                    'default_language'  => 'en',
                ];

                $userRepo->store($input);
            } catch (Exception $e) {
                echo '<pre>';
                print_r($e->getMessage());
                die;
            }
        }

        $user = User::create([
            'name' => 'Mr. Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('12345678'),
            'default_language'  => 'en',
        ]);
        $customerRole = Role::whereName('Customer')->first();
        $user->assignRole($customerRole);
    }
}
