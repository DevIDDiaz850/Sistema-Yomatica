<?php

namespace App\Filament\Resources\Facturas\Schemas;

use App\Models\Producto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FacturaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Receptor y Pago')->schema([
                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->relationship('cliente', 'razon_social')
                        ->required()
                        ->searchable(),

                    Select::make('uso_cfdi')
                        ->label('Uso de CFDI')
                        ->options([
                            'G01' => 'G01 - Adquisición de mercancías',
                            'G03' => 'G03 - Gastos en general',
                            'P01' => 'P01 - Por definir',
                            'I04' => 'I04 - Equipo de computo y accesorios',
                        ])
                        ->default('G03')
                        ->required(),

                    Select::make('forma_pago')
                        ->label('Forma de Pago')
                        ->options([
                            '01' => '01 - Efectivo',
                            '03' => '03 - Transferencia electrónica',
                            '04' => '04 - Tarjeta de crédito',
                            '28' => '28 - Tarjeta de débito',
                            '99' => '99 - Por definir',
                        ])
                        ->default('03')
                        ->required(),

                    Select::make('metodo_pago')
                        ->label('Método de Pago')
                        ->options([
                            'PUE' => 'PUE - Pago en una sola exhibición',
                            'PPD' => 'PPD - Pago en parcialidades o diferido',
                        ])
                        ->default('PUE')
                        ->required(),
                ])->columns(2),

                Section::make('Conceptos (Productos a Facturar)')->schema([
                    Repeater::make('conceptos')
                        ->label('')
                        ->schema([
                            Select::make('producto_id')
                                ->label('Producto')
                                ->options(Producto::pluck('nombre', 'id'))
                                ->required()
                                ->searchable()
                                ->columnSpan(3),

                            TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(1)
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->required(),
                    ])
            ]);
    }
}
