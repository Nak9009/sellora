<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|unique:users',
            'password' => 'required|min:8|confirmed',
            'terms' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered',
            'password.confirmed' => 'Passwords do not match',
            'terms.accepted' => 'You must accept the terms and conditions',
        ];
    }
}
