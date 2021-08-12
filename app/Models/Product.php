<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'code', 'price', 'qty', 'cost', 'min',
        'max', 'ws_min', 'ws_price', 'category',
        'unit', 'taxes'
    ];

    static $listed = [
        'title', 'code', 'price', 'qty',
    ];

    static $required = [
        'title', 'code', 'price'
    ];

    static $rules = [
        'title' => 'max:31',
        'code' => 'alpha_dash|max:127|unique:products,code',
        'price' => 'numeric|min:0',
        'qty' => 'nullable|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'min' => 'nullable|numeric|min:0',
        'max' => 'nullable|numeric|min:0',
        'ws_min' => 'nullable|numeric|min:0',
        'ws_price' => 'nullable|numeric|min:0',
        'category_id' => 'nullable|integer|exists:categories,id',
        'unit' => 'nullable|boolean',
        'taxes' => 'nullable|string',
    ];

    public function scopeFilter($query, $params)
    {
        if (isset($params['title']) && trim($params['title'] !== ''))
            $query->where('title', 'LIKE', trim($params['title']) . '%');

        return $query;
    }
}
