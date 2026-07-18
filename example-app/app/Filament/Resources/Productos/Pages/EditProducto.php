<?php

namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use App\Services\FacturapiProductoService;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Exception;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    protected function afterSave(): void
    {
        $producto = $this->record;
        $servicio = app(FacturapiProductoService::class);

        try {
            if ($producto->facturapi_id) {
                $servicio->actualizar($producto);
            } else {
                $servicio->crear($producto);
            }

            Notification::make()
                ->title('Producto actualizado en Facturapi')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error de sincronización con Facturapi')
                ->body($e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
