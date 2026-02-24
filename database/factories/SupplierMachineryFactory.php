<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EntityStatusEnum;
use App\Enums\TruckTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierMachinery>
 */
final class SupplierMachineryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => null,
            'license_plate' => $this->faker->unique()->regexify('[A-Z]{2,3}\-\d{3,4}'),
            'status' => EntityStatusEnum::INACTIVE,
            'nationality' => 'Peruana',
            'is_internal' => false,
            'truck_type' => TruckTypeEnum::T3,
            'has_bonus' => false,
            'tare' => $this->faker->numberBetween(4, 10),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::ACTIVE,
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => true,
        ]);
    }

    public function withBonus(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_bonus' => true,
        ]);
    }
}
