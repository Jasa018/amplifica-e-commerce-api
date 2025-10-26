<?php

namespace Database\Factories;

use App\Models\Cotizacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CotizacionFactory extends Factory
{
    protected $model = Cotizacion::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'region' => $this->faker->randomElement(['Metropolitana', 'Valparaíso', 'Biobío']),
            'comuna' => $this->faker->randomElement(['Santiago', 'Viña del Mar', 'Concepción']),
            'peso_total' => $this->faker->randomFloat(2, 0.5, 50),
            'productos' => [
                [
                    'name' => $this->faker->word(),
                    'weight' => $this->faker->randomFloat(2, 0.1, 10),
                    'quantity' => $this->faker->numberBetween(1, 5)
                ]
            ],
            'tarifas' => [
                [
                    'name' => 'Express',
                    'price' => $this->faker->numberBetween(3000, 8000)
                ],
                [
                    'name' => 'Estándar',
                    'price' => $this->faker->numberBetween(2000, 5000)
                ]
            ]
        ];
    }
}