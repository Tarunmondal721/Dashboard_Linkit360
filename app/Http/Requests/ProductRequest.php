<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'analytical_id' => 'required',
            'doman' => 'required',
            'name' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'analytical_id.required' => '*Please enter analytical id',
            'doman.required' => '*Please enter doman ',
            'name.required' => '*Please enter name ',
        ];
    }
}
