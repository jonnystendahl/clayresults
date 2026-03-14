<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    protected $model = Club::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Clay Club',
            'address' => fake()->streetAddress().', '.fake()->city(),
            'contact_person_name' => fake()->name(),
            'contact_person_email' => fake()->safeEmail(),
            'contact_person_phone' => fake()->phoneNumber(),
            'note' => fake()->sentence(),
        ];
    }
}