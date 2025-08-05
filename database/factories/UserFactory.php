<?php

namespace Database\Factories;

use App\Models\BusinessExperience;
use App\Models\PhotoGallery;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserEducation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->uniqueEmail(),
            'password'          => Hash::make('12345678'),
            'role'              => $this->faker->randomElement(['admin', 'user']),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
            'agree_to_terms'    => true,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            //! Create associated records after creating a user
            $user->profile()->save(Profile::factory()->make());
            $user->userAddresses()->saveMany(UserAddress::factory()->count(2)->make());
            $user->userEducations()->saveMany(UserEducation::factory()->count(3)->make());
            $user->photoGalleries()->saveMany(PhotoGallery::factory()->count(4)->make());
            $user->businessExperiences()->saveMany(BusinessExperience::factory()->count(3)->make());
        });
    }

    /**
     * Indicate that the model's email should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Generate a unique email.
     *
     * @return string
     */
    private function uniqueEmail(): string {
        static $counter = 1;
        return 'user' . $counter++ . '@gmail.com';
    }
}
