<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddGigRequest extends FormRequest
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
            'tags' => ['required', 'array'],
            'min_salary' => ['required', 'numeric'], 
            'max_salary' => ['required', 'numeric'],
            'role' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500']
        ];
        // Because of the timeline, country, state and address will be captured as strings instead of foreign keys.

    }

     /**
     * Prepare the data for validation.
     *
     * @return void
     */

    protected function cleanArray($arr) {
        if(!is_array($arr)) return $arr;
        $result = [];
        foreach($arr as $key => $value) {
            $result[] = \strip_tags($value);
        }
        return $result;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'min_salary' => \strip_tags($this->min_salary),
            'max_salary' => \strip_tags($this->max_salary),
            'role' => \strip_tags($this->role),
            'company' => \strip_tags($this->company),
            'country' => \strip_tags($this->country),
            'state' => \strip_tags($this->state),
            'address' => \strip_tags($this->address),
            'tags' => $this->cleanArray($this->tags)
        ]);
    }
}
