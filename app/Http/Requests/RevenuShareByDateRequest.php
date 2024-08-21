<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevenuShareByDateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'month' => 'required',
            'year' => 'required',
            'merchant_revenue_share' => 'required',
            'operator_revenue_share' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'month.required' => '*Please select month ',
            'year.required' => '*Please enter year ',
            'merchant_revenue_share.required' => '*Please enter merchant revenue share',
            'operator_revenue_share.required' => '*Please enter operator revenue share',
        ];
    }
}
