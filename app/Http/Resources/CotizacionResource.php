<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotizacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'region' => $this->region,
            'comuna' => $this->comuna,
            'peso_total' => $this->peso_total,
            'productos' => $this->productos,
            'tarifas' => $this->tarifas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}