<?php

declare(strict_types=1);

use App\Models\MachineryRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('MachineryRequirement Field Restrictions', function () {
    it('units quantity cannot be edited after announcement is launched', function () {
        $requirement = MachineryRequirement::factory()
            ->launchedAnnouncement()
            ->create();

        expect($requirement->isUnitsQuantityEditable())->toBeFalse();
    });

    it('units quantity can be edited before announcement is launched', function () {
        $requirement = MachineryRequirement::factory()->create();

        expect($requirement->isUnitsQuantityEditable())->toBeTrue();
    });

    it('activation time is locked after the scheduled time', function () {
        $requirement = MachineryRequirement::factory()
            ->create(['activation_time' => now()->subHour()]);

        expect($requirement->isActivationTimeLocked())->toBeTrue();
    });

    it('activation time is not locked before the scheduled time', function () {
        $requirement = MachineryRequirement::factory()
            ->create(['activation_time' => now()->addDay()]);

        expect($requirement->isActivationTimeLocked())->toBeFalse();
    });
});

describe('MachineryRequirement Model Creation', function () {
    it('can create machinery requirement with all required fields', function () {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $requirement = MachineryRequirement::factory()
            ->create([
                'user_id' => $user->id,
                'cost_center' => 'zzzzceco7777zzzz',
                'vessel_name' => 'MSC Elma',
                'units_quantity' => 5,
            ]);

        expect($requirement)->not->toBeNull()
            ->and($requirement->user_id)->toBe($user->id)
            ->and($requirement->cost_center)->toBe('zzzzceco7777zzzz')
            ->and($requirement->vessel_name)->toBe('MSC Elma')
            ->and($requirement->units_quantity)->toBe(5);
    });

    it('can create machinery requirement with announcement launched', function () {
        $requirement = MachineryRequirement::factory()
            ->launchedAnnouncement()
            ->create();

        expect($requirement->announcement_launched_at)->not->toBeNull();
    });
});
