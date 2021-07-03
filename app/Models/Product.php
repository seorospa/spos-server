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

    public function scopeFilter($query, $params)
    {
        if (isset($params['title']) && trim($params['title'] !== ''))
            $query->where('title', 'LIKE', trim($params['title']) . '%');

        return $query;
    }
}
