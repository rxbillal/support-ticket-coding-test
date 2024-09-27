<?php

namespace Database\Seeders;

use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CreateCategorySeeder extends Seeder
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
                'name'  => 'Customer Services',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Services',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Password Reset',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Data Restore',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Technical questions',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Billing issues',
                'color' => $faker->hexColor,
            ],
            [
                'name'  => 'Server issues',
                'color' => $faker->hexColor,
            ],
        ];

        foreach ($input as $data) {
            Category::create($data);
        }
    }
}
