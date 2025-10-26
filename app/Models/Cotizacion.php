<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'user_id',
        'region',
        'comuna',
        'region_origen',
        'comuna_origen',
        'region_destino',
        'comuna_destino',
        'peso_total',
        'productos',
        'tarifas',
        'tarifa_seleccionada',
        'tipo_tarifa'
    ];

    protected $casts = [
        'productos' => 'array',
        'tarifas' => 'array',
        'peso_total' => 'decimal:2',
        'tarifa_seleccionada' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}