<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'clave_producto_sat',
        'clave_unidad_sat',
        'tasa_iva',
        'facturapi_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'tasa_iva' => 'float',
    ];
}
