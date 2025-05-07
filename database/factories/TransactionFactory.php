<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['earned', 'spent', 'payout']);

        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'type' => $type,
            'status' => $type === 'payout' ? $this->faker->randomElement(['requested', 'approved', 'paid']) : null,
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'created_at' => now()->subMinutes(rand(0, 1000)),
            'updated_at' => now(),
        ];
    }
}
