<?php

namespace App\Services;

use Facturapi\Facturapi;
use GuzzleHttp\Client as GuzzleClient;
use App\Models\Cliente;
use Exception;

class FacturapiClienteService
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

    public function crear(Cliente $cliente)
    {
        $cpLimpio = preg_replace('/\D/', '', (string) $cliente->codigo_postal);
        $zip = str_pad($cpLimpio, 5, '0', STR_PAD_LEFT);

        if (empty($cpLimpio) || strlen($zip) !== 5) {
            throw new Exception('El código postal es obligatorio y debe contener 5 dígitos.');
        }

        $data = [
            'legal_name' => (string) $cliente->razon_social,
            'tax_id'     => strtoupper(trim((string) $cliente->rfc)),
            'tax_system' => (string) $cliente->regimen_fiscal,
            'address'    => [
                'zip' => $zip,
            ],
        ];

        if (!empty($cliente->email)) {
            $data['email'] = (string) $cliente->email;
        }

        $response = $this->facturapi->Customers->create($data);

        $cliente->update([
            'facturapi_id' => $response->id,
        ]);

        return $response;
    }

    public function crearCliente(Cliente $cliente)
    {
        return $this->crear($cliente);
    }
}
