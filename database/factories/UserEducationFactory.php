<?php

namespace Database\Factories;

use App\Models\UserEducation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserEducationFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserEducation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array {
        //! Generate a random start date for the academic year
        $academic_year_start = $this->faker->dateTimeBetween('-10 years', '-1 years');

        //! Generate a random end date for the academic year that is after the start date
        $academic_year_end = $this->faker->dateTimeBetween($academic_year_start);

        return [
            'degree'              => $this->faker->randomElement(['BSc', 'MSc', 'PhD']),
            'institute'           => $this->faker->company,
            'academic_year_start' => $academic_year_start->format('Y'),
            'academic_year_end'   => $academic_year_end->format('Y'),
        ];
    }
}
