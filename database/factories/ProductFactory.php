<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    protected static $productName = [
        'adjective' => [
            'Pequeño', 'Ergonómico', 'Rústico', 'Smart', 'Increíble',
            'Fantástico', 'Práctico', 'Moderno', 'Enorme', 'Mediocre',
            'Profesional', 'Ligero', 'Aerodinámico', 'Durable'
        ],
        'material' => [
            'Acero', 'Metal', 'Concreto', 'Plástico', 'Algodón', 'Cemento',
            'Goma', 'Cuero', 'Seda', 'Lana', 'Lino', 'Mármol', 'Fierro',
            'Bronce', 'Cobre', 'Aluminio', 'Papel'
        ],
        'product' => [
            'Silla', 'Auto', 'Computador', 'Guantes', 'Pantalones', 'Camisa',
            'Mesa', 'Zapatos', 'Sombrero', 'Plato', 'Cuchillo', 'Botella', 'Abrigo',
            'Lámpara', 'Teclado', 'Bolso', 'Banco', 'Reloj', 'Billetera'
        ],
    ];

    public function productName()
    {
        return $this->faker->randomElement(static::$productName['product'])
            . ' de ' . $this->faker->randomElement(static::$productName['material'])
            . ' ' . $this->faker->randomElement(static::$productName['adjective']);
    }

    public function definition()
    {
        return [
            'title' => $this->productName(),
            'code' => $this->faker->ean8,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'qty' => $this->faker->numberBetween(1, 100),
            'cost' => function (array $attributes) {
                return $this->faker->randomFloat(2, 1, $attributes['price']);
            },
            'min' => $this->faker->numberBetween(1, 100),
            'max' => function (array $attributes) {
                return $this->faker->numberBetween($attributes['min'], 100);
            },
            'ws_min' => $this->faker->numberBetween(1, 100),
            'ws_price' => function (array $attributes) {
                return $this->faker->numberBetween($attributes['cost'], $attributes['price']);
            },
            'category' => Category::all()->random()->id,
            'unit' => '1',
            'taxes' => null,
        ];
    }
}
