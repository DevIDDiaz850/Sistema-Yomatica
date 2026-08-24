<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'razon_social',
        'rfc',
        'regimen_fiscal',
        'codigo_postal',
        'email',
        'uso_cfdi',
        'facturapi_id',
    ];

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
