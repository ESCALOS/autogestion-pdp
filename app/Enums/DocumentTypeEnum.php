<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    // DRIVER DOCS
    case DNI = 'dni';
    case LICENCIA_DE_CONDUCIR = 'licencia_de_conducir';
    case CURSO_PBIP = 'curso_pbip';
    case CURSO_SEGURIDAD_PORTUARIA = 'curso_seguridad_portuaria';
    case CURSO_MERCANCIAS = 'curso_mercancias';
    case SCTR = 'sctr';
    case INDUCCION_SEGURIDAD = 'induccion_seguridad';
    case DECLARACION_JURADA = 'declaracion_jurada';

    // TRUCK DOCS
    case TARJETA_PROPIEDAD = 'tarjeta_propiedad';
    case SOAT = 'soat';
    case POLIZA_SEGURO = 'poliza_seguro';
    case BONIFICACION = 'bonificacion';
    case HABILITACION_MTC = 'habilitacion_mtc';
    case REVISION_TECNICA = 'revision_tecnica';

    // CHASSIS DOCS
    case CHASSIS_TARJETA_PROPIEDAD = 'chassis_tarjeta_propiedad';
    case CHASSIS_HABILITACION_MTC = 'chassis_habilitacion_mtc';
    case CHASSIS_BONIFICACION = 'chassis_bonificacion';
    case CHASSIS_REVISION_TECNICA = 'chassis_revision_tecnica';

    public function getLabel(): string
    {
        return match ($this) {
            // DRIVER DOCS
            self::DNI => 'DNI',
            self::LICENCIA_DE_CONDUCIR => 'Licencia de conducir',
            self::CURSO_PBIP => 'Curso PBIP',
            self::CURSO_SEGURIDAD_PORTUARIA => 'Curso Seguridad Portuaria',
            self::CURSO_MERCANCIAS => 'Curso Mercancías Peligrosas',
            self::SCTR => 'SCTR',
            self::INDUCCION_SEGURIDAD => 'Inducción de Seguridad',
            self::DECLARACION_JURADA => 'Declaración Jurada',

            // TRUCK DOCS
            self::TARJETA_PROPIEDAD => 'Tarjeta de Propiedad',
            self::SOAT => 'SOAT',
            self::POLIZA_SEGURO => 'Póliza de Seguro',
            self::BONIFICACION => 'Bonificación',
            self::HABILITACION_MTC => 'Habilitación MTC',
            self::REVISION_TECNICA => 'Revisión Técnica',

            // CHASSIS DOCS
            self::CHASSIS_TARJETA_PROPIEDAD => 'Chassis Tarjeta de Propiedad',
            self::CHASSIS_HABILITACION_MTC => 'Chassis Habilitación MTC',
            self::CHASSIS_BONIFICACION => 'Chassis Bonificación',
            self::CHASSIS_REVISION_TECNICA => 'Chassis Revisión Técnica',
        };
    }

    public function getColor(): string
    {
        return 'primary';
    }

    public function getIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    /**
     * Get the validity period in years for courses.
     * Returns null for non-course documents.
     */
    public function getValidityYears(): ?int
    {
        return match ($this) {
            self::CURSO_PBIP => 3,
            self::CURSO_SEGURIDAD_PORTUARIA => 3,
            self::CURSO_MERCANCIAS => 2,
            self::DECLARACION_JURADA => 1,
            self::INDUCCION_SEGURIDAD => 1,
            default => null,
        };
    }

    /**
     * Check if this document type requires a course date instead of expiration date.
     */
    public function requiresCourseDate(): bool
    {
        return in_array($this, [
            self::CURSO_PBIP,
            self::CURSO_SEGURIDAD_PORTUARIA,
            self::CURSO_MERCANCIAS,
            self::DECLARACION_JURADA,
            self::INDUCCION_SEGURIDAD,
        ]);
    }

    /**
     * Get the standardized file name for storage.
     * This ensures consistency between create forms and appeal forms.
     */
    public function getFileName(): string
    {
        return mb_strtoupper(str_replace(' ', '_', $this->getLabel()));
    }
}
