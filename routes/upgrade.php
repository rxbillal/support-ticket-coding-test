<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// upgrade to v2.0.0
Route::get('/upgrade-to-v2-0-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2021_09_01_000000_add_uuid_to_failed_jobs_table.php',
        ]);
});

// upgrade to v2.1.0
Route::get('/upgrade-to-v2-1-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2019_09_16_051035_create_conversations_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2019_11_19_054306_create_message_action_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2019_12_13_035642_create_blocked_users_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2019_12_19_052201_add_hard_delete_field_into_message_action_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2020_03_25_113611_create_notifications_table.php',
        ]);
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2020_04_02_075922_create_archived_users_table.php',
        ]);
});

// upgrade to v2.2.0
Route::get('/upgrade-to-v2-2-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2021_09_28_051126_create_user_notifications_table.php',
        ]);
});

// upgrade to v2.3.0
Route::get('/upgrade-to-v2-3-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2021_10_09_054344_add_email_updates_field_into_users.php',
        ]);
});

// upgrade to v2.4.0
Route::get('/upgrade-to-v2-4-0', function () {
    Artisan::call('migrate',
        [
            '--force' => true,
            '--path'  => 'database/migrations/2021_10_25_072026_create_social_accounts_table.php',
        ]);
});


Route::get('upgrade/database', function () {
    if (config('app.upgrade_mode')) {
        Artisan::call('migrate', ['--force' => true]);
    }
});

Route::get('lang-js', function () {
    Artisan::call('lang:js');
});

