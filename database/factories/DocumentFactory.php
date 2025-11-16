<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
final class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'documentable_type' => Driver::class,
            'documentable_id' => Driver::factory(),
            'type' => $this->faker->randomElement(DocumentTypeEnum::cases()),
            'path' => 'documents/'.$this->faker->uuid().'.pdf',
            'status' => DocumentStatusEnum::PENDING,
            'submitted_date' => now(),
            'expiration_date' => now()->addYear(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatusEnum::APPROVED,
            'validated_date' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DocumentStatusEnum::APPROVED,
            'rejection_reason' => $this->faker->sentence(),
            'validated_date' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expiration_date' => now()->subDay(),
        ]);
    }
}
