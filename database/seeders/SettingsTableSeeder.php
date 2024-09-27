<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imageUrl = 'assets/img/support-logo.png';
        $favicon = 'support.svg';
        // $favicon = 'favicon.ico';

        Setting::create(['key' => 'application_name', 'value' => 'Support']);
        Setting::create(['key' => 'logo', 'value' => $imageUrl]);
        Setting::create(['key' => 'favicon', 'value' => $favicon]);
        Setting::create(['key' => 'company_description', 'value' => 'Leading Laravel Development Company of BD']);
        Setting::create([
            'key'   => 'address',
            'value' => 'Dhaka, Bangladesh.',
        ]);
        Setting::create(['key' => 'phone', 'value' => '+880 1700000000']);
        Setting::create(['key' => 'email', 'value' => 'contact@support.com']);
        Setting::create(['key' => 'facebook_url', 'value' => 'https://www.facebook.com/support/']);
        Setting::create(['key' => 'twitter_url', 'value' => 'https://twitter.com/support?lang=en']);
        Setting::create([
            'key'   => 'linkedIn_url',
            'value' => 'https://www.linkedin.com',
        ]);
        Setting::create([
            'key'   => 'about_us',
            'value' => 'Over past 1+ years of experience and skills in various technologies, we built great scalable products.',
        ]);
    }
}
