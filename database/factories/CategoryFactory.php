<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    protected static $categoryNames = [
      'Bebestibles',
      'Pan',
      'Conservas',
      'Lácteos',
      'Congelados',
      'Carnes',
      'Verduras',
      'Limpieza',
      'Papelería',
      'Cuidado personal',
      'Otros',
    ];

    public function categoryName() {
      if (count(static::$categoryNames) > 0) {
        return array_pop(static::$categoryNames);
      }
      return $this->faker->unique()->regexify('[A-Z][a-z]{2,10}');
    }

    public function definition() {
      return [
        'father' => 0,
        'name' => $this->categoryName(),
      ];
    }
}
