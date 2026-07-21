<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaDownloadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/facturas/{factura}/descargar/{formato}', [FacturaDownloadController::class, 'descargar'])
    ->name('facturas.descargar')
    ->middleware(['auth']);
