<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EntityStatusEnum;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chassis>
 */
final class ChassisFactory extends Factory
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
            'license_plate' => strtoupper($this->faker->bothify('???-###')),
            'status' => EntityStatusEnum::PENDING_APPROVAL,
            'vehicle_type' => $this->faker->randomElement(['SEMI-REMOLQUE', 'REMOLQUE', 'CARRETA']),
            'axle_count' => $this->faker->numberBetween(2, 4),
            'has_bonus' => $this->faker->boolean(),
            'tare' => $this->faker->numberBetween(5000, 10000),
            'safe_weight' => $this->faker->numberBetween(15000, 30000),
            'height' => $this->faker->randomFloat(2, 2.5, 4.0),
            'length' => $this->faker->randomFloat(2, 10.0, 15.0),
            'width' => $this->faker->randomFloat(2, 2.4, 2.6),
            'is_insulated' => $this->faker->boolean(),
            'material' => $this->faker->randomElement(['ALUMINIO', 'ACERO', 'FIBRA DE VIDRIO']),
            'accepts_20ft' => $this->faker->boolean(),
            'accepts_40ft' => $this->faker->boolean(),
        ];
    }

    /**
     * Indica que el chassis está aprobado.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::ACTIVE,
        ]);
    }

    /**
     * Indica que el chassis está rechazado con token de apelación.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntityStatusEnum::REJECTED,
            'appeal_token' => Str::random(64),
            'appeal_token_expires_at' => now()->addDays(7),
        ]);
    }
}
