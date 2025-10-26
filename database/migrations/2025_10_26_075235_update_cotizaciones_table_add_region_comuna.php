<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->string('region')->nullable()->after('user_id');
            $table->string('comuna')->nullable()->after('region');
            
            // Hacer opcionales los campos antiguos
            $table->string('region_origen')->nullable()->change();
            $table->string('comuna_origen')->nullable()->change();
            $table->string('region_destino')->nullable()->change();
            $table->string('comuna_destino')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn(['region', 'comuna']);
        });
    }
};