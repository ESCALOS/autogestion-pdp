<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Company;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('dni')
                            ->label('DNI')
                            ->required()
                            ->minLength(8)
                            ->maxLength(20)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nombres')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255),
                        TextInput::make('lastname')
                            ->label('Apellidos')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255),
                        Select::make('company_id')
                            ->label('Empresa')
                            ->options(Company::query()
                                ->orderBy('business_name')
                                ->pluck('business_name', 'id'))
                            ->columnSpanFull(),
                        Toggle::make('is_company_representative')
                            ->label('Representante de la Empresa')
                            ->onIcon(Phosphor::CheckDuotone)
                            ->offIcon(Phosphor::XDuotone)
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->onIcon(Phosphor::CheckDuotone)
                            ->offIcon(Phosphor::XDuotone)
                            ->columnSpan(1),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->required()
                            ->prefixIcon(Phosphor::EnvelopeDuotone)
                            ->email()
                            ->columnSpanFull(),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->confirmed()
                            ->revealable()
                            ->prefixIcon(Phosphor::PasswordDuotone)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->columnSpan(1),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->password()
                            ->revealable()
                            ->prefixIcon(Phosphor::PasswordDuotone)
                            ->columnSpan(1),
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->prefixIcon(Phosphor::ShieldCheckDuotone)
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ])->columnSpan('4'),
            ])
            ->columns(6);
    }
}
