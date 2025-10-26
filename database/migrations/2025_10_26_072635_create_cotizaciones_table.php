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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('region_origen');
            $table->string('comuna_origen');
            $table->string('region_destino');
            $table->string('comuna_destino');
            $table->decimal('peso_total', 8, 2);
            $table->json('productos');
            $table->json('tarifas');
            $table->decimal('tarifa_seleccionada', 10, 2)->nullable();
            $table->string('tipo_tarifa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
