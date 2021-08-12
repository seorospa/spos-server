<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'client', 'status'
    ];

    protected $casts = [
        'cart' => 'array'
    ];

    static $listed = [
        'id', 'name', 'user_id', 'status',
    ];


    static $required = [
        'name',
    ];

    static $rules = [
        'name' => 'max:15'
    ];

    public function scopeFilter($query, $params)
    {
        if (isset($params['status']))
            $query->where('status', $params['status']);

        return $query;
    }
}

