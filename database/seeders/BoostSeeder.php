<?php

namespace Database\Seeders;

use App\Models\Boost;
use Illuminate\Database\Seeder;

class BoostSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Boost::create([
            'name'     => 'Boost',
            'price'    => 9.99,
            'duration' => 30,
        ]);
    }
}
