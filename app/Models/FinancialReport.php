<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialReport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'summary' => 'array',
        'note' => 'array',
    ];
}
