<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'government_id',
        'email',
        'debt_amount',
        'debt_due_date',
        'debt_id',
        'processed',
    ];
    
    protected $casts = [
        'debt_due_date' => 'date',
        'processed' => 'boolean',
    ];
}