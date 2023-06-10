<?php

namespace App\Services;

use App\Models\ExtraRepaymentSchedule;
use App\Models\LoanAmortizationSchedule;
use App\Models\LoanDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LoanService
{

    /**
     * Save Loan details, amortization schedules and extra repayment schedules
     *
     * @param array $loanData
     * @return LoanDetail
     */
    public function saveLoanDetails(array $loanData) : LoanDetail
    {
        // Save the loan details and calculation results to the database
        $loanDetail = LoanDetail::create($loanData);

        // Generate the amortization schedule
        $amortizationSchedule = $this->generateAmortizationSchedule($loanDetail);
        LoanAmortizationSchedule::insert($amortizationSchedule);


        // Generate the schedule with recalculated, shortened loans due to extra payments
        $extraRepaymentSchedule = $this->generateExtraRepaymentSchedule($loanDetail);
        ExtraRepaymentSchedule::insert($extraRepaymentSchedule);

        return $loanDetail;
    }

    /**
     * Generate Amortization Schedule
     *
     * @param LoanDetail $loanDetail
     * @return array
     */
    private function generateAmortizationSchedule(LoanDetail $loanDetail) : array
    {
        $loanAmount = $loanDetail->loan_amount;
        $monthlyInterestRate = ($loanDetail->annual_interest / 12) / 100;
        $numberOfMonths = $loanDetail->month_number;
        $loanDetailId = $loanDetail->id;

        // Monthly payment
        $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$numberOfMonths));

        $startingBalance = $loanAmount;
        $schedule = [];

        for ($month = 1; $month <= $numberOfMonths; $month++) {
            $interest = $startingBalance * $monthlyInterestRate;
            $principal = $monthlyPayment - $interest;

            $endingBalance = $startingBalance - $principal;

            $schedule[] = [
                'loan_detail_id' => $loanDetailId,
                'month_number' => $month,
                'starting_balance' => $startingBalance,
                'monthly_payment' => $monthlyPayment,
                'principal' => $principal,
                'interest' => $interest,
                'ending_balance' => $endingBalance,
            ];

            $startingBalance = $endingBalance;
        }

        return $schedule;
    }

    /**
     * Generate Extra Repayment Schedule.
     *
     * @param LoanDetail $loanDetail
     * @return array
     */
    private function generateExtraRepaymentSchedule(LoanDetail $loanDetail) : array
    {
        $extraRepaymentSchedule = [];

        $remainingLoanTerm = $loanDetail->month_number;
        $startingBalance = intval($loanDetail->loan_amount);
        $loanDetailId = $loanDetail->id;
        $extraPayment = $loanDetail->monthly_fixed_extra_payment;
        $loanDetailId = $loanDetail->id;
        $monthlyInterestRate = ($loanDetail->annual_interest / 12) / 100;

        // Monthly payment
        $monthlyPayment = ($startingBalance * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$remainingLoanTerm));
        $month = 1;
        $extraRepaymentSchedule = [];

        while ($startingBalance > 0 && $remainingLoanTerm >= 0) {
            $interest = $startingBalance * $monthlyInterestRate;
            $principal = $monthlyPayment - $interest;
            $remainingLoanBalance = $startingBalance - $principal;

            if ($monthlyPayment > $startingBalance) {
                $monthlyPayment = $startingBalance;
                $principal = $startingBalance - $interest;
                $extraPayment = 0;
                $remainingLoanBalance = 0;
            }

            // Apply extra payment if available
            if ($extraPayment > 0) {
                $remainingLoanBalance -= $extraPayment;
            }

            $extraRepaymentSchedule[] = [
                'month_number' => $month,
                'starting_balance' => $startingBalance,
                'monthly_payment' => $monthlyPayment,
                'principal' => $principal,
                'interest' => $interest,
                'extra_repayment' => $extraPayment,
                'ending_balance' => $remainingLoanBalance,
                'loan_detail_id' => $loanDetailId,
            ];

            $remainingLoanTerm--;
            $startingBalance = $remainingLoanBalance;
            $month++;
        }

        return $this->reCalculateRemainingLoanTerms($extraRepaymentSchedule);
    }

    /**
     * Calculate the remaining Loan terms for extra repayment.
     *
     * @param array $extraRepaymentSchedule
     * @return array
     */
    private function reCalculateRemainingLoanTerms(array $extraRepaymentSchedule): array
    {
        $totalLoanTerms = count($extraRepaymentSchedule);
        $rePaymentSchedules = [];
        // recalculate remaining loan terms.
        foreach ($extraRepaymentSchedule as $data) {
            $data['remaining_loan_term'] = $totalLoanTerms;
            $rePaymentSchedules[] = $data;
            $totalLoanTerms--;
        }
        return $rePaymentSchedules;
    }

    /**
     * Function to get the Loan Details.
     *
     * @param LoanDetail $loanDetail
     * @return array
     */
    public function getLoanDetails(LoanDetail $loanDetail) : array
    {
        // Retrieve the loan amortization record by its ID
        $loanDetail = LoanDetail::findOrFail($loanDetail->id);

        // Retrieve the effective interest rate
        $effectiveInterestRate = $this->calculateEffectiveInterestRate($loanDetail);

        // Retrieve the amortization schedule
        $amortizationSchedule = LoanAmortizationSchedule::where('loan_detail_id', $loanDetail->id)->get();

        // Retrieve the extra repayment schedule
        $extraRepaymentSchedule = $this->getExtraRepaymentSchedule($loanDetail);
        $loanAmortization = $loanDetail;

        return [$loanAmortization, $effectiveInterestRate, $amortizationSchedule, $extraRepaymentSchedule];
    }

    /**
     * Calculate effective interest Rate
     *
     * @param LoanDetail $loanDetail
     * @return float
     */
    private function calculateEffectiveInterestRate(LoanDetail $loanDetail): float
    {
        // Get the remaining loan balance
        $remainingBalance = $loanDetail->loan_amount;

        if($loanDetail->monthly_fixed_extra_payment <= 0) {
            return $loanDetail->annual_interest;
        }

        // Retrieve the extra repayment schedule
        $extraRepaymentSchedule = $this->getExtraRepaymentSchedule($loanDetail);

        // Deduct the extra repayment amounts from the remaining balance
        foreach ($extraRepaymentSchedule as $row) {
            $remainingBalance -= $row->extra_repayment;
        }

        // Calculate the effective interest rate based on the remaining balance
        $monthlyInterestRate = ($loanDetail->annual_interest / 12) / 100;
        $effectiveInterestRate = (($remainingBalance * $monthlyInterestRate) / ($loanDetail->month_number * 12));

        return $effectiveInterestRate;
    }

    /**
     * Get Extra Repayment Schedule data.
     *
     * @param LoanDetail $loanDetail
     * @return Collection
     */
    private function getExtraRepaymentSchedule(LoanDetail $loanDetail): Collection
    {
        // Retrieve the extra repayment schedule from the database based on the loan amortization ID
        return ExtraRepaymentSchedule::where('loan_detail_id', $loanDetail->id) ->get();
    }

}
