<?php

namespace App\Filament\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),

                TextColumn::make('precio')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('clave_sat')
                    ->label('C. SAT'),

                TextColumn::make('taxability')
                    ->label('Objeto Imp.'),

                TextColumn::make('facturapi_id')
                    ->label('Facturapi ID')
                    ->copyable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
