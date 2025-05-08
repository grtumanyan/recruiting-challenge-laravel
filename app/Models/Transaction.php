<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'status',
        'amount',
    ];

    // Automatically assign a unique UUID to the every new Transaction
    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->id = (string) Str::uuid();
        });
    }
}
