<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultCountryCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countryCodeExist = Setting::where('key', 'default_country_code')->exists();

        if (!$countryCodeExist) {
            Setting::create(['key' => 'default_country_code', 'value' => 'bd']);
        }

        $countryRegionCodeExist = Setting::where('key', 'default_region_code')->exists();

        if (!$countryRegionCodeExist) {
            Setting::create(['key' => 'default_region_code', 'value' => '880']);
        }

    }
}
