<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DriverStatusEnum;
use App\Models\Driver;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class DriverAppealController extends Controller
{
    public function show(string $token)
    {
        $driver = Driver::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->with(['documents', 'company'])
            ->firstOrFail();

        // Obtener documentos rechazados o vencidos
        $rejectedDocuments = $driver->documents()
            ->where(function ($query) {
                $query->where('status', 3) // 3 = rejected
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        if ($rejectedDocuments->isEmpty()) {
            abort(404, 'No hay documentos rechazados o vencidos para actualizar.');
        }

        return view('driver-appeal', compact('driver', 'rejectedDocuments', 'token'));
    }

    public function update(Request $request, string $token)
    {
        $driver = Driver::where('appeal_token', $token)
            ->where('appeal_token_expires_at', '>', now())
            ->firstOrFail();

        $rejectedDocuments = $driver->documents()
            ->where(function ($query) {
                $query->where('status', 3) // 3 = rejected
                    ->orWhere('expiration_date', '<', now());
            })
            ->get();

        // Validar archivos
        $rules = [];

        foreach ($rejectedDocuments as $document) {
            $rules["document_{$document->id}"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';

            // Si el documento tiene fecha de vencimiento, validar que se proporcione
            if ($document->expiration_date) {
                $rules["expiration_date_{$document->id}"] = 'required|date|after:today';
            }
        }

        $request->validate($rules, [
            '*.required' => 'Este documento es obligatorio.',
            '*.file' => 'Debe cargar un archivo válido.',
            '*.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG.',
            '*.max' => 'El archivo no debe superar los 5MB.',
            '*.date' => 'Debe proporcionar una fecha válida.',
            '*.after' => 'La fecha de vencimiento debe ser posterior a hoy.',
        ]);

        try {
            DB::beginTransaction();

            foreach ($rejectedDocuments as $document) {
                $fieldName = "document_{$document->id}";

                if ($request->hasFile($fieldName)) {
                    // Eliminar documento anterior
                    if ($document->path && Storage::disk('public')->exists($document->path)) {
                        Storage::disk('public')->delete($document->path);
                    }

                    // Subir nuevo documento
                    $file = $request->file($fieldName);
                    $filename = $driver->document_number.'_'.$document->type->value.'_'.time().'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs('driver-documents/'.$driver->document_number, $filename, 'public');

                    // Actualizar documento a pendiente
                    $updateData = [
                        'path' => $path,
                        'status' => 1, // 1 = pending
                        'rejection_reason' => null,
                        'validated_by' => null,
                        'validated_date' => null,
                        'submitted_date' => now(),
                    ];

                    // Actualizar fecha de vencimiento si se proporcionó
                    if ($request->has("expiration_date_{$document->id}")) {
                        $updateData['expiration_date'] = $request->input("expiration_date_{$document->id}");
                    }

                    $document->update($updateData);
                }
            }

            // Actualizar estado del conductor a revisión de documentos y limpiar token
            $driver->update([
                'status' => DriverStatusEnum::DOCUMENT_REVIEW,
                'appeal_token' => null,
                'appeal_token_expires_at' => null,
            ]);

            DB::commit();

            return redirect()->route('driver.appeal.success');

        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Ocurrió un error al procesar la actualización. Por favor, intente nuevamente.');
        }
    }

    public function success()
    {
        return view('driver-appeal-success');
    }
}
