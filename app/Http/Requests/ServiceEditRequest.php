<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceEditRequest extends FormRequest
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
            // 'country' => 'required',
            // 'company' => 'required',
            // 'operator' => 'required',
            // 'servicename' => 'required',
            // 'subkeyword' => 'required',
            // 'short_code' => 'required',
            'product_brief_file' => 'file|max:10240',
            'faq_file' => 'file|max:10240',
            'contract_file' => 'file|max:10240',
            'merchant_coi_file' => 'file|max:10240',
            'addendums_file' => 'file|max:10240',
            'content_authority_letter' => 'file|max:10240',
            'cor_dgt_file' => 'file|max:10240'
        ];
    }
    public function messages()
    {
        return [
            'product_brief_file.max' => '*Maximum File Size 10MB',
            'faq_file.max' => '*Maximum File Size 10MB',
            'contract_file.max' => '*Maximum File Size 10MB',
            'merchant_coi_file.max' => '*Maximum File Size 10MB',
            'addendums_file.max' => '*Maximum File Size  10MB',
            'content_authority_letter.max' => '*Maximum File Size  10MB',
            'cor_dgt_file.max' => '*Maximum File Size  10MB',
            // 'country.required' => '*Please select country ',
            // 'company.required' => '*Please select company ',
            // 'operator.required' => '*Please select operator ',
            // 'servicename.required' => '*Please enter service name ',
            // 'subkeyword.required' => '*Please enter subkeyword ',
            // 'short_code.required' => '*Please enter short code ',
        ];
    }
}
