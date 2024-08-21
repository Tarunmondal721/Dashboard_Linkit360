<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Session;
class UserOperator extends FormRequest
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
            'operators' => 'required',
        ];
    }
    public function messages()
    {
        // Session::flash('error', '*Please select operators');
        return [
            'operators.required' => '*Please select operators ',
        ];
    }
}
