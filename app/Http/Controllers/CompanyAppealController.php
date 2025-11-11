<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CompanyDocumentStatusEnum;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class CompanyAppealController extends Controller
{
    public function show(string $token)
    {
        $company = Company::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->with(['documents', 'representative'])
            ->firstOrFail();

        // Obtener solo los documentos rechazados
        $rejectedDocuments = $company->documents()
            ->where('status', CompanyDocumentStatusEnum::RECHAZADO)
            ->get();

        if ($rejectedDocuments->isEmpty()) {
            abort(404, 'No hay documentos rechazados para apelar.');
        }

        return view('company-appeal', compact('company', 'rejectedDocuments', 'token'));
    }

    public function update(Request $request, string $token)
    {
        $company = Company::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->firstOrFail();

        $rejectedDocuments = $company->documents()
            ->where('status', CompanyDocumentStatusEnum::RECHAZADO)
            ->get();

        $rules = [];
        foreach ($rejectedDocuments as $document) {
            $rules["document_{$document->id}"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $request->validate($rules, [
            '*.required' => 'Este documento es obligatorio.',
            '*.file' => 'Debe cargar un archivo válido.',
            '*.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG.',
            '*.max' => 'El archivo no debe superar los 5MB.',
        ]);

        try {
            DB::beginTransaction();

            foreach ($rejectedDocuments as $document) {
                $fieldName = "document_{$document->id}";

                if ($request->hasFile($fieldName)) {
                    // Eliminar documento anterior
                    if ($document->path && Storage::disk('s3')->exists($document->path)) {
                        Storage::disk('s3')->delete($document->path);
                    }

                    // Subir nuevo documento
                    $file = $request->file($fieldName);
                    $path = $file->store('company-documents', 's3');

                    // Actualizar documento a pendiente
                    $document->update([
                        'path' => $path,
                        'status' => CompanyDocumentStatusEnum::PENDIENTE,
                        'rejection_reason' => null,
                        'validated_by' => null,
                        'validated_date' => null,
                        'submitted_date' => now(),
                    ]);
                }
            }

            // Actualizar estado de la empresa a pendiente y limpiar token
            $company->update([
                'status' => 1, // Pendiente
                'appeal_token' => null,
                'appeal_token_expires_at' => null,
            ]);

            DB::commit();

            return redirect()->route('company.appeal.success');

        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocurrió un error al procesar su apelación. Por favor, intente nuevamente.');
        }
    }

    public function success()
    {
        return view('company-appeal-success');
    }
}
