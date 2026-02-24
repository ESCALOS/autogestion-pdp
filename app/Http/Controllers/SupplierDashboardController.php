<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EntityStatusEnum;
use App\Models\SupplierDriver;
use App\Models\SupplierMachinery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SupplierDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $supplier = $request->user()->supplier;
        $supplierId = $supplier->id;

        $approvedStatuses = [EntityStatusEnum::ACTIVE->value];
        $pendingStatuses = [
            EntityStatusEnum::PENDING_APPROVAL->value,
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
                'route' => 'supplier.drivers.index',
                'model' => SupplierDriver::class,
                'icon' => 'driver',
            ],
            [
                'key' => 'machinery',
                'title' => 'Maquinaria',
                'description' => 'Gestión de maquinaria',
                'route' => 'supplier.machinery.index',
                'model' => SupplierMachinery::class,
                'icon' => 'truck',
            ],
        ];

        $stats = [];
        foreach ($modules as $module) {
            $stats[$module['key']] = array_merge(
                $module,
                ['stats' => $this->getEntityStats($module['model'], $supplierId, $approvedStatuses, $pendingStatuses, $rejectedStatuses)]
            );
        }

        return view('supplier.dashboard', ['supplier' => $supplier, 'modules' => $stats]);
    }

    private function getEntityStats(
        string $modelClass,
        int $supplierId,
        array $approvedStatuses,
        array $pendingStatuses,
        array $rejectedStatuses
    ): array {
        $stats = $modelClass::query()
            ->where('supplier_id', $supplierId)
            ->select(
                DB::raw('COUNT(CASE WHEN status IN ('.implode(',', array_map(fn ($s) => "'$s'", $approvedStatuses)).') THEN 1 END) as approved'),
                DB::raw('COUNT(CASE WHEN status IN ('.implode(',', array_map(fn ($s) => "'$s'", $pendingStatuses)).') THEN 1 END) as pending'),
                DB::raw('COUNT(CASE WHEN status IN ('.implode(',', array_map(fn ($s) => "'$s'", $rejectedStatuses)).') THEN 1 END) as rejected')
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
