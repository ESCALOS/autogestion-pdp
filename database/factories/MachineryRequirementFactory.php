<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MachineryOperationTypeEnum;
use App\Enums\MachineryUnitTypeEnum;
use App\Models\MachineryRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MachineryRequirement>
 */
final class MachineryRequirementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $operationTypes = MachineryOperationTypeEnum::cases();
        $unitTypes = MachineryUnitTypeEnum::cases();

        return [
            'user_id' => User::factory(),
            'cost_center' => mb_strtoupper($this->faker->bothify('????ceco####????')),
            'vessel_name' => $this->faker->randomElement(['MSC Elma', 'CMA CGM', 'Maersk', 'COSCO', 'Evergreen']),
            'operation_type' => $this->faker->randomElement($operationTypes),
            'unit_type' => $this->faker->randomElement($unitTypes),
            'units_quantity' => $this->faker->numberBetween(1, 20),
            'activation_time' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'days_quantity' => $this->faker->numberBetween(1, 30),
            'announcement_launched_at' => null,
        ];
    }

    public function launchedAnnouncement(): static
    {
        return $this->state(fn (array $attributes) => [
            'announcement_launched_at' => now(),
        ]);
    }
}
