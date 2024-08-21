<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PivotRequest extends FormRequest
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

            'date' => 'required',
            'data' => 'required',
            'type' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'type.required' => '*Please select type ',
            'data.required' => '*Please select data ',
            'date.required' => '*Please select date ',
        ];
    }
}
