<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierDriver>
 */
final class SupplierDriverFactory extends Factory
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
            'document_type' => DriverDocumentTypeEnum::DNI,
            'document_number' => $this->faker->unique()->numerify('########'),
            'name' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'license_number' => $this->faker->unique()->numerify('###########'),
            'status' => EntityStatusEnum::INACTIVE,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::ACTIVE,
        ]);
    }

    public function carneExtranjeria(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => DriverDocumentTypeEnum::CE,
        ]);
    }
}
