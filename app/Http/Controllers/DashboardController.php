<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EntityStatusEnum;
use App\Models\Chassis;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $companyId = $request->user()->company_id;

        $approvedStatuses = [EntityStatusEnum::ACTIVE->value];
        $pendingStatuses = [
            EntityStatusEnum::PENDING_APPROVAL->value
        ];
        $rejectedStatuses = [
            EntityStatusEnum::INACTIVE->value,
            EntityStatusEnum::NEEDS_UPDATE->value,
            EntityStatusEnum::REJECTED->value,
        ];

        $modules = [
            [
                'key' => 'drivers',
                'title' => 'Conductores',
                'description' => 'Gestión de conductores',
                'route' => 'drivers.index',
                'model' => Driver::class,
                'icon' => 'driver',
            ],
            [
                'key' => 'trucks',
                'title' => 'Camiones',
                'description' => 'Gestión de vehículos',
                'route' => 'trucks.index',
                'model' => Truck::class,
                'icon' => 'truck',
            ],
            [
                'key' => 'chassis',
                'title' => 'Carretas',
                'description' => 'Gestión de carretas',
                'route' => 'chassis.index',
                'model' => Chassis::class,
                'icon' => 'chassis',
            ],
        ];

        $stats = [];
        foreach ($modules as $module) {
            $stats[$module['key']] = array_merge(
                $module,
                ['stats' => $this->getEntityStats($module['model'], $companyId, $approvedStatuses, $pendingStatuses, $rejectedStatuses)]
            );
        }

        return view('dashboard', ['modules' => $stats]);
    }

    private function getEntityStats(
        string $modelClass,
        int $companyId,
        array $approvedStatuses,
        array $pendingStatuses,
        array $rejectedStatuses
    ): array {
        $stats = $modelClass::query()
            ->where('company_id', $companyId)
            ->select(
                DB::raw('COUNT(CASE WHEN status IN (' . implode(',', array_map(fn($s) => "'$s'", $approvedStatuses)) . ') THEN 1 END) as approved'),
                DB::raw('COUNT(CASE WHEN status IN (' . implode(',', array_map(fn($s) => "'$s'", $pendingStatuses)) . ') THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN status IN (' . implode(',', array_map(fn($s) => "'$s'", $rejectedStatuses)) . ') THEN 1 END) as rejected')
            )
            ->first();

        return [
            'approved' => $stats->approved ?? 0,
            'pending' => $stats->pending ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'total' => ($stats->approved ?? 0) + ($stats->pending ?? 0) + ($stats->rejected ?? 0),
        ];
    }
}
