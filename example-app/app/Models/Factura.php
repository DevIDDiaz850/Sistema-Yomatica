<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'forma_pago',
        'metodo_pago',
        'uso_cfdi',
        'conceptos',
        'facturapi_id',
        'uuid_sat',
        'url_pdf',
        'url_xml',
    ];

    protected $casts = [
        'conceptos' => 'array',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
