<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\User\Models\Retailer>
 */
class RetailerFactory extends Factory
{
    protected $model = \App\Modules\User\Models\Retailer::class;

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
