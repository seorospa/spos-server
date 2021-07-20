<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  use HasFactory;

  protected $fillable = [
    'ammount', 'reason', 'debit_or_credit', 'user_id'
  ];
}
