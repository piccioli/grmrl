<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CaiSectionSeeder::class,
            ActivitySeeder::class,
            AdminSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call(FakeRegistrationsSeeder::class);
        }
    }
}
