<?php

namespace App\Filament\Resources\Facturas\Tables;

use App\Models\Factura;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use App\Services\FacturapiFacturaService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Exception;

class FacturasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Folio Int.')
                    ->sortable(),

                TextColumn::make('cliente.razon_social')
                ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('uuid_sat')
                    ->label('Folio Fiscal (UUID)')
                    ->copyable()
                    ->searchable()
                    ->placeholder('Pendiente de timbrar...'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('descargar_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn ($record) => $record->url_pdf)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->url_pdf !== null),

                Action::make('descargar_xml')
                    ->label('XML')
                    ->icon('heroicon-o-code-bracket')
                    ->color('info')
                    ->url(fn ($record) => $record->url_xml)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->url_xml !== null),
                Action::make('pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn ($record) => route('facturas.descargar', ['factura' => $record->id, 'formato' => 'pdf']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->facturapi_id !== null),

                Action::make('xml')
                    ->label('Descargar XML')
                    ->icon('heroicon-o-code-bracket')
                    ->color('info')
                    ->url(fn ($record) => route('facturas.descargar', ['factura' => $record->id, 'formato' => 'xml']))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->facturapi_id !== null),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
        ->defaultSort('created_at', 'desc');
    }
}
