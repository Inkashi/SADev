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
