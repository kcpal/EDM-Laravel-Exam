<!-- resources/views/loan/form.blade.php -->
@extends('layouts.app')

@section('content')

<form method="POST" action="{{ route('loan.calculate') }}">
    @csrf

    <div class="form-group">
        <label for="loan_amount">Loan Amount:</label>
        <input type="text" class="form-control" id="loan_amount" name="loan_amount" value="{{ old('loan_amount') }}">
        @error('loan_amount')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="annual_interest">Annual Interest Rate:</label>
        <input type="text" class="form-control" id="annual_interest" name="annual_interest" value="{{ old('annual_interest') }}">
        @error('annual_interest')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="loan_term">Loan Term (in years):</label>
        <input type="text" class="form-control" id="loan_term" name="loan_term" value="{{ old('loan_term') }}">
        @error('loan_term')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="monthly_fixed_extra_payment">Monthly Fixed Extra Payment:</label>
        <input type="text" class="form-control" id="monthly_fixed_extra_payment" name="monthly_fixed_extra_payment" value="{{ old('monthly_fixed_extra_payment') }}">
        @error('monthly_fixed_extra_payment')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Calculate</button>
</form>
@endsection
