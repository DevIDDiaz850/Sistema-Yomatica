<?php

namespace App\Services;

use Facturapi\Facturapi;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use Exception;
use Throwable;

class FacturapiFacturaService
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


    public function timbrar(Factura $factura, array $conceptos = [])
    {
        try {
            if (empty($conceptos)) {
                $conceptos = $factura->conceptos ?? [];
            }

            if (empty($conceptos)) {
                throw new Exception('Debes agregar al menos un producto o concepto a la factura.');
            }

            $cliente = $factura->cliente ?? Cliente::find($factura->cliente_id);

            if (!$cliente) {
                throw new Exception('La factura no tiene un cliente válido asignado.');
            }

            $items = [];
            foreach ($conceptos as $item) {
                $productoModel = !empty($item['producto_id'])
                    ? Producto::find($item['producto_id'])
                    : null;

                $descripcion = $item['descripcion']
                    ?? $item['description']
                    ?? $item['nombre']
                    ?? $productoModel?->descripcion
                    ?? $productoModel?->nombre
                    ?? 'Concepto';

                $claveSat = $item['clave_producto_sat']
                    ?? $item['product_key']
                    ?? $productoModel?->clave_producto_sat
                    ?? '01010101';

                $unidadSat = $item['clave_unidad_sat']
                    ?? $item['unit_key']
                    ?? $productoModel?->clave_unidad_sat
                    ?? 'H87';

                $precio = (float) (
                    $item['precio_unitario']
                    ?? $item['price']
                    ?? $item['precio']
                    ?? $productoModel?->precio
                    ?? 0
                );

                $cantidad = (int) ($item['cantidad'] ?? $item['quantity'] ?? 1);

                $productData = [
                    'description' => (string) $descripcion,
                    'product_key' => (string) $claveSat,
                    'unit_key'    => (string) $unidadSat,
                    'price'       => $precio,
                ];

                if ($productoModel && isset($productoModel->tasa_iva)) {
                    $productData['taxes'] = [
                        [
                            'type' => 'IVA',
                            'rate' => (float) $productoModel->tasa_iva,
                        ]
                    ];
                }

                $items[] = [
                    'quantity' => $cantidad,
                    'product'  => $productData,
                ];
            }

            $cpLimpio = preg_replace('/\D/', '', (string) $cliente->codigo_postal);
            $zip = str_pad($cpLimpio, 5, '0', STR_PAD_LEFT);

            $customerData = [
                'legal_name' => (string) $cliente->razon_social,
                'tax_id'     => strtoupper(trim((string) $cliente->rfc)),
                'tax_system' => (string) $cliente->regimen_fiscal,
                'address'    => [
                    'zip' => $zip,
                ],
            ];

            if (!empty($cliente->email)) {
                $customerData['email'] = (string) $cliente->email;
            }

            $data = [
                'customer' => !empty($cliente->facturapi_id)
                    ? $cliente->facturapi_id
                    : $customerData,
                'items'          => $items,
                'payment_form'   => (string) ($factura->forma_pago ?? '03'),
                'payment_method' => (string) ($factura->metodo_pago ?? 'PUE'),
                'use'            => (string) ($factura->uso_cfdi ?? $cliente->uso_cfdi ?? 'G03'),
            ];

            $facturaApiObj = $this->facturapi->Invoices->create($data);

            $factura->update([
                'facturapi_id' => $facturaApiObj->id,
                'uuid_sat'     => $facturaApiObj->uuid ?? null,
                'url_pdf'      => $facturaApiObj->verification_url ?? null,
            ]);

            return $facturaApiObj;

        } catch (Throwable $e) {
            throw new Exception("Error en Facturapi: " . $e->getMessage());
        }
    }

    public function descargarPdf(string $facturapiId)
    {
        return $this->facturapi->Invoices->download_pdf($facturapiId);
    }

    public function descargarXml(string $facturapiId)
    {
        return $this->facturapi->Invoices->download_xml($facturapiId);
    }
}
