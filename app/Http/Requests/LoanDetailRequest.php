<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'loan_amount' => 'required|numeric|min:0',
            'annual_interest' => 'required|numeric|min:0',
            'loan_term' => 'required|numeric|min:0',
            'monthly_fixed_extra_payment' => 'nullable|numeric|min:0',
        ];
    }
}
