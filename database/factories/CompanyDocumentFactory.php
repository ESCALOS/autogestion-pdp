<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CompanyDocumentStatusEnum;
use App\Enums\CompanyDocumentTypeEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyDocument>
 */
final class CompanyDocumentFactory extends Factory
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
            'type' => $this->faker->randomElement(CompanyDocumentTypeEnum::cases()),
            'path' => 'company-documents/'.$this->faker->uuid().'.pdf',
            'status' => CompanyDocumentStatusEnum::PENDIENTE,
            'submitted_date' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyDocumentStatusEnum::APROBADO,
            'validated_date' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompanyDocumentStatusEnum::RECHAZADO,
            'rejection_reason' => $this->faker->sentence(),
            'validated_date' => now(),
        ]);
    }
}
