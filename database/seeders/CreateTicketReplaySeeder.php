<?php

namespace Database\Seeders;

use App\Models\User;
use App\Repositories\TicketReplayRepository;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CreateTicketReplaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::pluck('id')->toArray();
        $ticketReplay = App::make(TicketReplayRepository::class);
        $faker = Faker::create();
        foreach (range(1, 30) as $index) {
            try {
                $input = [
                    'ticket_id'   => rand(1, 10),
                    'description' => $faker->realText(200),
                ];

                Auth::loginUsingId(Arr::random($users));

                $ticketReplay->store($input);
            } catch (Exception $e) {
                echo '<pre>';
                print_r($e->getMessage());
                die;
            }
        }
    }
}
