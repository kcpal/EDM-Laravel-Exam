<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAmortizationSchedule extends Model
{
    use HasFactory;

    protected $table = 'loan_amortization_schedule';

    protected $fillable = [
        'loan_detail_id',
        'starting_balance',
        'principal',
        'interest',
        'monthly_payment',
        'month_number',
        'ending_balance'
    ];
}
