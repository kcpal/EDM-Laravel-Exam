<!-- resources/views/loan/show.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Loan Details</div>
                    <div class="card-body">
                        <h5>Loan Setup Details:</h5>
                        <p><strong>Loan Amount:</strong> ${{ $loanAmortization->loan_amount }}</p>
                        <p><strong>Annual Interest Rate:</strong> {{ $loanAmortization->annual_interest }}%</p>
                        <p><strong>Loan Term:</strong> {{ $loanAmortization->month_number/12 }} years</p>
                        <p><strong>Extra Payment:</strong> ${{ $loanAmortization->monthly_fixed_extra_payment }}</p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">Amortization Schedule</div>
                    <div class="card-body">
                        <h5>Effective Interest Rate: {{ round($loanAmortization->annual_interest, 2) }}%</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Starting Balance</th>
                                    <th>Monthly Payment</th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>Ending Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($amortizationSchedule as $row)
                                    <tr>
                                        <td>{{ $row->month_number }}</td>
                                        <td>{{ $row->starting_balance }}</td>
                                        <td>{{ $row->monthly_payment }}</td>
                                        <td>{{ $row->principal }}</td>
                                        <td>{{ $row->interest }}</td>
                                        <td>{{ $row->ending_balance }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">Schedule with Extra Repayments</div>
                    <div class="card-body">
                        <h5>Effective Interest Rate: {{ round($effectiveInterestRate, 2) }}%</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Starting Balance</th>
                                    <th>Monthly Payment</th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>Extra Repayment Made</th>
                                    <th>Ending Balance</th>
                                    <th>Remaining Loan Term</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($extraRepaymentSchedule as $row)
                                    <tr>
                                        <td>{{ $row->month_number }}</td>
                                        <td>{{ $row->starting_balance }}</td>
                                        <td>{{ $row->monthly_payment }}</td>
                                        <td>{{ $row->principal }}</td>
                                        <td>{{ $row->interest }}</td>
                                        <td>{{ $row->extra_repayment }}</td>
                                        <td>{{ $row->ending_balance }}</td>
                                        <td>{{ $row->remaining_loan_term }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
