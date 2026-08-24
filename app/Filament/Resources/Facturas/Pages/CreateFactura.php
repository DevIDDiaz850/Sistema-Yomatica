<?php

namespace App\Filament\Resources\Facturas\Pages;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Services\FacturapiFacturaService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Exception;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $conceptos = $data['conceptos'] ?? [];

        $factura = static::getModel()::create($data);

        try {
            $servicio = app(FacturapiFacturaService::class);
            $servicio->timbrar($factura, $conceptos);

            Notification::make()
                ->title('¡Factura Timbrada con Éxito!')
                ->success()
                ->send();

        } catch (Exception $e) {
            $factura->delete();

            Notification::make()
                ->title('Error al timbrar')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        return $factura;
    }
}
