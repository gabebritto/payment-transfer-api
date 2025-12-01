<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retailer>
 */
class RetailerFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone_number' => fake()->unique()->phoneNumber(),
            'password' => static::$password ??= \Illuminate\Support\Facades\Hash::make('password'),
            'document' => fake()->unique()->numerify('##############'), // CNPJ 14 digits
        ];
    }
}
