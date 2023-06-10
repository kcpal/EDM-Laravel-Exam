<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraRepaymentSchedule extends Model
{
    use HasFactory;

    protected $table = 'extra_repayment_schedule';

    protected $fillable = [
        'loan_detail_id',
        'month_number',
        'starting_balance',
        'monthly_payment',
        'principal',
        'interest',
        'extra_repayment',
        'ending_balance',
        'remaining_loan_term',
    ];

    public function loanAmortizationSchedule()
    {
        return $this->belongsTo(LoanAmortizationSchedule::class, 'loan_amortization_id');
    }

}
