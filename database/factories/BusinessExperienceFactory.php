<?php

namespace Database\Factories;

use App\Models\BusinessExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessExperienceFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessExperience::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array {
        //! Generate a random start date for the experience
        $experience_from = $this->faker->dateTimeBetween('-10 years', '-1 years');

        //! Generate a random end date for the experience that is after the start date
        $experience_to = $this->faker->dateTimeBetween($experience_from);

        return [
            'meta_id'             => $this->faker->numberBetween(1, 8),
            'industry'            => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Education', 'Manufacturing', 'Retail', 'Other']),
            'other_industry'      => $this->faker->optional()->sentence,
            'years_of_experience' => $this->faker->randomElement(['Less than 1 year', '1-3 years', '3-5 years', '5-10 years', 'More than 10 years']),
            'areas_of_expertise'  => $this->faker->randomElement(['Marketing', 'Sales', 'Operations', 'Finance', 'Product Development', 'Legal', 'Technology', 'Other']),
            'other_expertise'     => $this->faker->optional()->sentence,
            'support_offer'       => $this->faker->randomElement(['Mentorship', 'Investment', 'Partnership', 'Advisory', 'Networking', 'Other']),
            'other_support_offer' => $this->faker->optional()->sentence,
            'designation'         => $this->faker->jobTitle,
            'company_name'        => $this->faker->company,
            'experience_from'     => $experience_from->format('Y'),
            'experience_to'       => $experience_to->format('Y'),

        ];
    }
}
