<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'father_id',
    ];

    static $listed = [
        'name', 'father_id',
    ];

    public function scopeFilter($query, $params)
    {
        return $query;
    }
}
