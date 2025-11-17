<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EntityStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Truck>
 */
final class TruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'license_plate' => mb_strtoupper($this->faker->unique()->bothify('???-###')),
            'status' => EntityStatusEnum::DOCUMENT_REVIEW,
            'nationality' => $this->faker->randomElement(['PE', 'CL', 'BO', 'BR', 'AR']),
            'is_internal' => $this->faker->boolean(),
            'truck_type' => $this->faker->randomElement(['Tracto', 'Volquete', 'Camión']),
            'has_bonus' => $this->faker->boolean(30),
            'tare' => $this->faker->numberBetween(5000, 15000),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::ACTIVE,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::REJECTED,
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => true,
            'nationality' => 'PE',
        ]);
    }

    public function external(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => false,
        ]);
    }
}
