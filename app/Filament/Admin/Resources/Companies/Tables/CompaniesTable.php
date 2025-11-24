<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Companies\Tables;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Filament\Exports\CompanyExporter;
use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruc')
                    ->label('RUC')
                    ->searchable(),

                TextColumn::make('business_name')
                    ->label('Razón Social')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo de Empresa')
                    ->options(CompanyTypeEnum::class),
                Filter::make('date_range')
                    ->schema([
                        Select::make('date_type')
                            ->label('Tipo de Fecha')
                            ->options([
                                'created_at' => 'Fecha de Creación',
                                'updated_at' => 'Fecha de Actualización',
                            ])
                            ->default('created_at')
                            ->required(),
                        DatePicker::make('date_from')
                            ->label('Desde'),
                        DatePicker::make('date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dateType = $data['date_type'] ?? 'created_at';

                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate($dateType, '>=', $date),
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate($dateType, '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        $dateType = $data['date_type'] ?? 'created_at';
                        $dateLabel = $dateType === 'created_at' ? 'Creación' : 'Actualización';

                        if ($data['date_from'] ?? null) {
                            $indicators[] = $dateLabel.' desde '.\Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators[] = $dateLabel.' hasta '.\Carbon\Carbon::parse($data['date_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('validate')
                    ->label('Validar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn (Company $record): string => CompanyResource::getUrl('view', ['record' => $record]))
                    ->visible(fn (Company $record): bool => $record->status !== CompanyStatusEnum::APROBADO),

                EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->exporter(CompanyExporter::class),
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(CompanyExporter::class),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }
}
