<?php

namespace App\Services;

use Facturapi\Facturapi;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\Producto;

class FacturapiProductoService
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

    public function crear(Producto $producto)
    {
        $data = [
            'description' => (string) ($producto->descripcion ?? $producto->nombre),
            'product_key' => (string) ($producto->clave_producto_sat ?? '01010101'),
            'price'       => (float) $producto->precio,
            'unit_key'    => (string) ($producto->clave_unidad_sat ?? 'H87'),
        ];

        if (isset($producto->tasa_iva)) {
            $data['taxes'] = [
                [
                    'type' => 'IVA',
                    'rate' => (float) $producto->tasa_iva,
                ]
            ];
        }

        $response = $this->facturapi->Products->create($data);

        $producto->update([
            'facturapi_id' => $response->id,
        ]);

        return $response;
    }
}
