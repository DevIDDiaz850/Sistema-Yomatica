<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('facturapi_id')->nullable()->unique();
            $table->string('razon_social');
            $table->string('rfc', 13);
            $table->string('regimen_fiscal', 3);
            $table->string('codigo_postal', 5);
            $table->string('uso_cfdi_defecto', 3)->default('G03');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
