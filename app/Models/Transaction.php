<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'reason', 'debit_or_credit', 'user'
    ];

    public function scopeFilter($query, $params)
    {
        return $query;
    }
}
