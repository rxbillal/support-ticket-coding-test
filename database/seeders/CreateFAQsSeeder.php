<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CreateFAQsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $input = [
            [
                'title'       => 'Do you have a demo of your service?',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'Are there any limitations in the Trial account or Live Demo?',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'How do I add the chat feature to my website?',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'Where is My CashBack?',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'My Payment is Failed',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'Are there any limitations Live Demo?',
                'description' => $faker->text(150),
            ],
            [
                'title'       => 'geting Server issues',
                'description' => $faker->text(150),
            ],
        ];

        foreach ($input as $data) {
            FAQ::create($data);
        }
    }
}
