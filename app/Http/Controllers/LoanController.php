<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanDetailRequest;
use App\Models\LoanDetail;
use App\Services\LoanService;
use Exception;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        return view('loan.form');
    }

    public function calculate(LoanDetailRequest $request)
    {
        $loanData = $request->validated();
        $loanData['month_number'] = $loanData['loan_term'] * 12;

        $loanService = new LoanService();
        $loanDetail = $loanService->saveLoanDetails($loanData);

        return redirect()->route('loan.show', ['loanDetail' => $loanDetail->id]);
    }

    public function show(LoanDetail $loanDetail)
    {
        $loanService = new LoanService();
        [$loanAmortization, $effectiveInterestRate, $amortizationSchedule, $extraRepaymentSchedule] = $loanService->getLoanDetails($loanDetail);

        // Pass the data to the view
        return view('loan.show', compact('loanAmortization', 'effectiveInterestRate', 'amortizationSchedule', 'extraRepaymentSchedule'));
    }

}
