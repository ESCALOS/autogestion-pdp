<?php

declare(strict_types=1);

namespace App\Livewire\Supplier;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard - Proveedores')]
final class SupplierDashboard extends Component
{
    public function render()
    {
        $supplier = Auth::guard('supplier')->user()?->supplier;

        return view('livewire.supplier.supplier-dashboard', compact('supplier'));
    }
}
