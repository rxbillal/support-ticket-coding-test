<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\TicketRepository;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CreateTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::pluck('id')->toArray();
        /** @var $ticketRepo TicketRepository */
        $ticketRepo = App::make(TicketRepository::class);
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            try {
                $input = [
                    'title'       => $faker->firstName,
                    'email'       => $faker->unique()->safeEmail,
                    'category_id' => rand(1, 7),
                    'description' => $faker->realText(),
                ];

                Auth::loginUsingId(Arr::random($users));

                $ticketRepo->store($input);
            } catch (Exception $e) {
                print_r($e->getMessage());
                die;
            }
        }
    }
}
