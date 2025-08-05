<?php

namespace Database\Factories;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array {
        //! Define arrays of cities, states, and provinces
        $cities    = ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Sylhet', 'Barisal', 'Rangpur', 'Comilla'];
        $states    = ['Dhaka Division', 'Chittagong Division', 'Khulna Division', 'Rajshahi Division', 'Sylhet Division', 'Barisal Division', 'Rangpur Division', 'Mymensingh Division'];
        $provinces = ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Sylhet', 'Barisal', 'Rangpur', 'Mymensingh'];

        return [
            'country'  => 'Bangladesh',
            'city'     => $this->faker->randomElement($cities),
            'state'    => $this->faker->randomElement($states),
            'province' => $this->faker->randomElement($provinces),
        ];
    }
}
