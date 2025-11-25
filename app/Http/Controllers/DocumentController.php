<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

final class DocumentController extends Controller
{
    public function company(CompanyDocument $document)
    {
        // Verificar que el usuario tenga permiso para ver este documento
        if (Auth::user()->hasRole('super_admin') || Auth::user()->company_id === $document->company_id) {
            // Generar URL temporal firmada válida por 5 minutos
            $temporaryUrl = Storage::temporaryUrl(
                $document->path,
                now()->addMinutes(5)
            );

            return redirect($temporaryUrl);
        }

        abort(403);
    }

    public function entity(Document $document)
    {
        // Verificar que el usuario tenga permiso para ver este documento
        if (Auth::user()->hasRole('super_admin') || Auth::user()->company_id === $document->company_id) {
            // Generar URL temporal firmada válida por 5 minutos
            $temporaryUrl = Storage::temporaryUrl(
                $document->path,
                now()->addMinutes(5)
            );

            return redirect($temporaryUrl);
        }

        abort(403);
    }
}
