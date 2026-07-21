<?php

namespace App\Services;

use Facturapi\Facturapi;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacturapiCatalogoService
{
    protected $facturapi;

    public function __construct()
    {
        $certPath = base_path('certs/cacert.pem');

        $httpClient = new GuzzleClient([
            'verify' => file_exists($certPath) ? $certPath : false,
        ]);

        $this->facturapi = new Facturapi(
            config('services.facturapi.key'),
            ['httpClient' => $httpClient]
        );
    }


    public function buscarProductos(string $search): array
    {
        $search = trim($search);

        if (strlen($search) < 2) {
            return [];
        }

        try {
            $results = $this->facturapi->Catalogs->searchProducts(['q' => $search]);

            $items = is_array($results) ? $results : ($results->data ?? []);
            $options = [];

            foreach ($items as $item) {
                $key = is_object($item) ? ($item->key ?? null) : ($item['key'] ?? null);
                $desc = is_object($item) ? ($item->description ?? '') : ($item['description'] ?? '');

                if ($key) {
                    $options[(string)$key] = "{$key} - {$desc}";
                }
            }

            return $options;
        } catch (Throwable $e) {
            Log::error("Error en FacturapiCatalogoService::buscarProductos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerNombreProducto($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            $results = $this->facturapi->Catalogs->searchProducts(['q' => (string) $value]);
            $items = is_array($results) ? $results : ($results->data ?? []);

            foreach ($items as $item) {
                $key = is_object($item) ? ($item->key ?? null) : ($item['key'] ?? null);
                $desc = is_object($item) ? ($item->description ?? '') : ($item['description'] ?? '');

                if ((string)$key === (string)$value) {
                    return "{$key} - {$desc}";
                }
            }

            return (string) $value;
        } catch (Throwable $e) {
            Log::error("Error en FacturapiCatalogoService::obtenerNombreProducto: " . $e->getMessage());
            return (string) $value;
        }
    }


    public function buscarUnidades(string $search): array
    {
        $search = trim($search);

        if (strlen($search) < 2) {
            return [];
        }

        try {
            $results = $this->facturapi->Catalogs->searchUnits(['q' => $search]);
            $items = is_array($results) ? $results : ($results->data ?? []);
            $options = [];

            foreach ($items as $item) {
                $key = is_object($item) ? ($item->key ?? null) : ($item['key'] ?? null);
                $name = is_object($item) ? ($item->name ?? '') : ($item['name'] ?? '');

                if ($key) {
                    $options[(string)$key] = "{$key} - {$name}";
                }
            }

            return $options;
        } catch (Throwable $e) {
            Log::error("Error en FacturapiCatalogoService::buscarUnidades: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerNombreUnidad($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            $results = $this->facturapi->Catalogs->searchUnits(['q' => (string) $value]);
            $items = is_array($results) ? $results : ($results->data ?? []);

            foreach ($items as $item) {
                $key = is_object($item) ? ($item->key ?? null) : ($item['key'] ?? null);
                $name = is_object($item) ? ($item->name ?? '') : ($item['name'] ?? '');

                if ((string)$key === (string)$value) {
                    return "{$key} - {$name}";
                }
            }

            return (string) $value;
        } catch (Throwable $e) {
            Log::error("Error en FacturapiCatalogoService::obtenerNombreUnidad: " . $e->getMessage());
            return (string) $value;
        }
    }
}
