<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Empresa')
                    ->schema([
                        Grid::make([
                            TextEntry::make('type')
                                ->label('Tipo')
                                ->badge(),
                            TextEntry::make('status')
                                ->label('Estado')
                                ->badge(),
                            IconEntry::make('is_active')
                                ->label('Activo')
                                ->boolean(),
                        ]),
                        TextEntry::make('ruc')
                            ->label('RUC')
                            ->copyable()
                            ->icon('heroicon-o-clipboard-document'),
                        TextEntry::make('business_name')
                            ->label('Razón Social')
                            ->copyable()
                            ->icon('heroicon-o-building-office-2'),
                        TextEntry::make('appeal_token_expires_at')
                            ->label('Token de Apelación Expira')
                            ->dateTime()
                            ->placeholder('-'),
                        Grid::make([
                            TextEntry::make('created_at')
                                ->label('Creado')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('updated_at')
                                ->label('Actualizado')
                                ->dateTime()
                                ->placeholder('-'),
                        ]),
                    ])
                    ->columns(2),

                Section::make('Documentos')
                    ->schema([
                        RepeatableEntry::make('documents')
                            ->label('')
                            ->schema([
                                Grid::make([
                                    TextEntry::make('type')
                                        ->label('Tipo de Documento')
                                        ->badge()
                                        ->grow(false),
                                    TextEntry::make('status')
                                        ->label('Estado')
                                        ->badge()
                                        ->grow(false),
                                ]),
                                TextEntry::make('path')
                                    ->label('Ruta del Archivo')
                                    ->formatStateUsing(fn ($state) => basename($state))
                                    ->icon('heroicon-o-document')
                                    ->iconColor('primary')
                                    ->copyable()
                                    ->url(fn ($record) => route('company.document.view', $record->id))
                                    ->openUrlInNewTab(),
                                ImageEntry::make('preview')
                                    ->label('Vista Previa')
                                    ->state(fn ($record) => route('company.document.view', $record->id))
                                    ->imageHeight(200)
                                    ->visible(fn ($record) => ! str_ends_with($record->path, '.pdf'))
                                    ->url(fn ($record) => route('company.document.view', $record->id))
                                    ->openUrlInNewTab(),
                                TextEntry::make('submitted_date')
                                    ->label('Fecha de Envío')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('validated_date')
                                    ->label('Fecha de Validación')
                                    ->date()
                                    ->placeholder('-'),
                                TextEntry::make('validator.name')
                                    ->label('Validado por')
                                    ->placeholder('-'),
                                TextEntry::make('rejection_reason')
                                    ->label('Razón de Rechazo')
                                    ->placeholder('-')
                                    ->visible(fn ($record) => $record->rejection_reason !== null)
                                    ->columnSpanFull()
                                    ->color('danger'),
                            ])
                            ->columns(2)
                            ->grid(1),
                    ])
                    ->collapsible(),
            ]);
    }
}
