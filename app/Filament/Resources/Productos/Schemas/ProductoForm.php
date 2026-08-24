<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Services\FacturapiCatalogoService;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255),

                TextInput::make('precio')
                    ->label('Precio (Sin IVA)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                Select::make('clave_sat')
                    ->label('Clave de Producto SAT')
                    ->required()
                    ->searchable()
                    ->default('01010101')
                    ->getSearchResultsUsing(fn (string $search) => app(FacturapiCatalogoService::class)->buscarProductos($search))
                    ->getOptionLabelUsing(fn ($value) => $value ? app(FacturapiCatalogoService::class)->obtenerNombreProducto($value) ?? $value : null),

                Select::make('unidad_sat')
                    ->label('Clave de Unidad SAT')
                    ->required()
                    ->searchable()
                    ->default('H87')
                    ->getSearchResultsUsing(fn (string $search) => app(FacturapiCatalogoService::class)->buscarUnidades($search))
                    ->getOptionLabelUsing(fn ($value) => $value ? app(FacturapiCatalogoService::class)->obtenerNombreUnidad($value) ?? $value : null),

                Select::make('taxability')
                    ->label('Objeto de Impuesto')
                    ->options([
                        '01' => '01 - No objeto de impuesto',
                        '02' => '02 - Sí objeto de impuesto (Lleva IVA)',
                        '03' => '03 - Sí objeto de impuesto y no obligado al desglose',
                        '04' => '04 - Sí objeto de impuesto y no causa impuesto',
                    ])
                    ->default('02')
                    ->required(),

                Textarea::make('descripcion')
                    ->label('Descripción extra')
                    ->columnSpanFull(),
            ]);
    }
}
