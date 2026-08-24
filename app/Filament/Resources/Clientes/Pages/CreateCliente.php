<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use App\Services\FacturapiClienteService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Exception;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;

    protected function afterCreate(): void
    {
        $cliente = $this->record;
        $servicio = app(FacturapiClienteService::class);

        try {
            $servicio->crear($cliente);

            Notification::make()
                ->title('Cliente registrado en BD y en Facturapi')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Cliente guardado localmente, pero falló en Facturapi')
                ->body($e->getMessage())
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
