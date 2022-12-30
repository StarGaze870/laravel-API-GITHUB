<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        return $user != null && $user->tokenCan('update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {        
        $method = $this->method();
       
        if ($method == 'PUT') {
            return [
                'customerId' => ['required', 'integer'],
                'amount' => ['required', 'numeric'],
                'status' => ['required', Rule::in(['B', 'P', 'V', 'b', 'p', 'v'])],            
                'billedDate' => ['required', 'date_format:Y-m-d H:i:s'],
                'paidDate' => ['date_format:Y-m-d H:i:s', 'nullable']
            ];  
        }
        else {
            return [
                'customerId' => ['sometimes', 'required', 'integer'],
                'amount' => ['sometimes', 'required', 'numeric'],
                'status' => ['sometimes', 'required', Rule::in(['B', 'P', 'V', 'b', 'p', 'v'])],            
                'billedDate' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s'],
                'paidDate' => ['sometimes', 'date_format:Y-m-d H:i:s', 'nullable']
            ];  
        }         
    }

    protected function prepareForValidation() {        

        if ($this->customerId) {
            $this->merge([
                'customer_id' => $this->customerId
            ]);
        }   
        if ($this->billedDate) {
            $this->merge([
                'billed_date' => $this->billedDate
            ]);
        }   
        if ($this->paidDate) {
            $this->merge([
                'paid_date' => $this->paidDate
            ]);
        }            
    }
}
