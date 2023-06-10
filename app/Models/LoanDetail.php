<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_amount',
        'month_number',
        'annual_interest',
        'monthly_fixed_extra_payment',
    ];
}
