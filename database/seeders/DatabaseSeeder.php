<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void {
        //! Call the MetaSeeder
        $this->call(MetaSeeder::class);

        //! Create 10 users using the User factory
        User::factory()->count(10)->create();

        //! Call the SubscriptionSeeder
        $this->call(SubscriptionSeeder::class);

        //! Call the BoostSeeder
        $this->call(BoostSeeder::class);
    }
}
