<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Facturapi\Facturapi;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Exception;

class FacturaDownloadController extends Controller
{
    public function descargar(Factura $factura, $formato)
    {
        if (!$factura->facturapi_id) {
            abort(404, 'Esta factura no ha sido timbrada en el SAT.');
        }

        if (!in_array($formato, ['pdf', 'xml'])) {
            abort(400, 'Formato no válido. Solo se permite pdf o xml.');
        }

        $apiKey = config('services.facturapi.key', '');
        $certPath = str_replace('\\', '/', base_path('storage/certs/cacert.pem'));
        $verifySSL = file_exists($certPath) ? $certPath : true;

        $httpClient = new GuzzleClient([
            'verify'  => $verifySSL,
            'timeout' => 30,
        ]);

        $facturapi = new Facturapi($apiKey, ['httpClient' => $httpClient]);

        try {
            if ($formato === 'pdf') {
                $contenido = $facturapi->Invoices->downloadPdf($factura->facturapi_id);
                $contentType = 'application/pdf';
                $nombreArchivo = "Factura_{$factura->uuid_sat}.pdf";
            } else {
                $contenido = $facturapi->Invoices->downloadXml($factura->facturapi_id);
                $contentType = 'application/xml';
                $nombreArchivo = "Factura_{$factura->uuid_sat}.xml";
            }

            return response($contenido)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', "inline; filename=\"{$nombreArchivo}\"");
        } catch (Exception $e) {
            abort(500, 'Error al conectar con Facturapi: ' . $e->getMessage());
        }
    }
}
