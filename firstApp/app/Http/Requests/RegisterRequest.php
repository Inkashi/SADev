<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RegisterDTO;

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

            'username' => 'required|string|unique:users|alpha|regex:/^[A-Z]/|min:7',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'c_password' => 'required|string|same:password',
            'birthday' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'The username is required.',
            'username.unique' => 'The username has already been taken.',
            'username.alpha' => 'The username must only contain letters.',
            'username.regex' => 'The username must start with an uppercase letter.',
            'username.min' => 'The username must be at least 7 characters.',
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
            'c_password.required' => 'The confirm password is required.',
            'c_password.same' => 'The confirm password must match the password.',
            'birthday.required' => 'The birthday is required.',
            'birthday.date' => 'The birthday must be a valid date.',
        ];
    }


    public function createDTO(): RegisterDTO
    {
        return new RegisterDTO(
            $this->input('username'),
            $this->input('email'),
            $this->input('password'),
            $this->input('birthday')
        );
    }
}
