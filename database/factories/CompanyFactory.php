<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
final class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => CompanyTypeEnum::NATURAL,
            'ruc' => $this->faker->unique()->numerify('10########'),
            'business_name' => $this->faker->company(),
            'status' => CompanyStatusEnum::PENDIENTE,
            'is_active' => false,
        ];
    }

    public function juridica(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CompanyTypeEnum::JURIDICA,
            'ruc' => $this->faker->unique()->numerify('20########'),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyStatusEnum::APROBADO,
            'is_active' => true,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyStatusEnum::RECHAZADO,
            'is_active' => false,
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Company $company) {
            // Crear automáticamente un representante para la empresa
            \App\Models\User::factory()->representative()->create([
                'company_id' => $company->id,
            ]);
        });
    }
}
