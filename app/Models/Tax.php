<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'percentage',
    ];

    static $listed = [
        'name', 'percentage',
    ];

    public function scopeFilter($query, $params)
    {
        if (isset($params['name']) && trim($params['name'] !== ''))
            $query->where('name', 'LIKE', trim($params['name']) . '%');

        return $query;
    }
}
