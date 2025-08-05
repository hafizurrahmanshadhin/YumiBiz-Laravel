<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory {

    protected $model = Profile::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'user_name' => $this->faker->unique()->userName,
            'age'       => $this->faker->numberBetween(21, 75),
            'gender'    => $this->faker->randomElement(['male', 'female']),
            'bio'       => $this->faker->text(199),
        ];
    }
}
