<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DriverDocumentTypeEnum;
use App\Enums\EntityStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
final class DriverFactory extends Factory
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
            'document_type' => DriverDocumentTypeEnum::DNI,
            'document_number' => $this->faker->unique()->numerify('########'),
            'name' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'license_number' => $this->faker->unique()->bothify('??-########'),
            'status' => EntityStatusEnum::PENDING_APPROVAL,
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
}
