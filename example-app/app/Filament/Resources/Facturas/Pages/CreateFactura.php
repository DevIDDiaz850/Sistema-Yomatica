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
        // 1. Obtener la lista de conceptos del formulario
        $conceptos = $data['conceptos'] ?? [];

        // 2. Guardar la factura localmente
        $factura = static::getModel()::create($data);

        // 3. Timbrar con Facturapi
        try {
            $servicio = app(FacturapiFacturaService::class);
            $servicio->timbrar($factura, $conceptos);

            Notification::make()
                ->title('¡Factura Timbrada con Éxito!')
                ->success()
                ->send();

        } catch (Exception $e) {
            // Si falla el timbrado, eliminamos el registro local
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
