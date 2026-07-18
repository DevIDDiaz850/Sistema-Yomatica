<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Services\FacturapiClienteService;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Exception;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;

    protected function afterSave(): void
    {
        $cliente = $this->record;
        $servicio = app(FacturapiClienteService::class);

        try {
            if ($cliente->facturapi_id) {
                $servicio->actualizar($cliente);
            } else {
                $servicio->crear($cliente);
            }

            Notification::make()
                ->title('Cliente actualizado en Facturapi')
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
