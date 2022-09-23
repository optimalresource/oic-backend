<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'max:25'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required!',
            'name.required' => 'Name is required!',
            'password.required' => 'Password is required!',
            'password_confirmation.required' => 'Password confirmation is required!',
        ];
    }

    /**
     * prepare the data for validation.
     * 
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strip_tags($this->name),
            'email' => strip_tags($this->email),
        ]);
    }
}
