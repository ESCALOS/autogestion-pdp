<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EntityStatusEnum;
use App\Models\Chassis;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        // Estados agrupados
        $approvedStatuses = [EntityStatusEnum::ACTIVE->value];
        $pendingStatuses = [
            EntityStatusEnum::PENDING_APPROVAL->value,
            EntityStatusEnum::DOCUMENT_REVIEW->value,
        ];
        $rejectedStatuses = [
            EntityStatusEnum::INACTIVE->value,
            EntityStatusEnum::NEEDS_UPDATE->value,
            EntityStatusEnum::INFECTED_DOCUMENTS->value,
            EntityStatusEnum::REJECTED->value,
        ];

        // Estadísticas de Drivers
        $driversApproved = Driver::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $approvedStatuses)
            ->count();

        $driversPending = Driver::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $pendingStatuses)
            ->count();

        $driversRejected = Driver::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $rejectedStatuses)
            ->count();

        // Estadísticas de Trucks
        $trucksApproved = Truck::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $approvedStatuses)
            ->count();

        $trucksPending = Truck::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $pendingStatuses)
            ->count();

        $trucksRejected = Truck::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $rejectedStatuses)
            ->count();

        // Estadísticas de Chassis
        $chassisApproved = Chassis::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $approvedStatuses)
            ->count();

        $chassisPending = Chassis::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $pendingStatuses)
            ->count();

        $chassisRejected = Chassis::query()
            ->where('company_id', $companyId)
            ->whereIn('status', $rejectedStatuses)
            ->count();

        return view('dashboard', [
            'drivers' => [
                'approved' => $driversApproved,
                'pending' => $driversPending,
                'rejected' => $driversRejected,
                'total' => $driversApproved + $driversPending + $driversRejected,
            ],
            'trucks' => [
                'approved' => $trucksApproved,
                'pending' => $trucksPending,
                'rejected' => $trucksRejected,
                'total' => $trucksApproved + $trucksPending + $trucksRejected,
            ],
            'chassis' => [
                'approved' => $chassisApproved,
                'pending' => $chassisPending,
                'rejected' => $chassisRejected,
                'total' => $chassisApproved + $chassisPending + $chassisRejected,
            ],
        ]);
    }
}
