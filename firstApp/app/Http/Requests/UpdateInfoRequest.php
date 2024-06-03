<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
            'new_username' => 'string|unique:users,username|alpha|regex:/^[A-Z]/|min:7|nullable',
            'new_email' => 'nullable|string|email|unique:users,email|nullable',
            'old_pass' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'new_pass' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/|nullable',
            'new_birthday' => 'nullable|date|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'new_username.unique' => 'The username has already been taken.',
            'new_username.alpha' => 'The username must only contain letters.',
            'new_username.regex' => 'The username must start with an uppercase letter.',
            'new_username.min' => 'The username must be at least 7 characters.',
            'new_email.email' => 'The email must be a valid email address.',
            'new_email.unique' => 'The email has already been taken.',
            'new_pass.required' => 'The password is required.',
            'new_pass.min' => 'The password must be at least 8 characters.',
            'new_pass.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'old_pass.min' => 'The password must be at least 8 characters.',
            'old_pass.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'new_birthday.required' => 'The birthday is required.',
            'new_birthday.date' => 'The birthday must be a valid date.',
        ];
    }
}
