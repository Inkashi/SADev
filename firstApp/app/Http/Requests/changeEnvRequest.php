<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class changeEnvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::check()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'MAX_ACTIVE_TOKENS' => 'integer|min:1|nullable',
            'TOKEN_EXPIRATION_DAYS' => '|integer|min:1|nullable ',
            'MAX_CODE_COUNT' => 'integer|min:1|nullable',
            'REFRESH_CODE_LIMIT' => 'integer|min:1|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'MAX_ACTIVE_TOKENS.min' => 'The MAX_ACTIVE_TOKENS must be at least 1.',
            'TOKEN_EXPIRATION_DAYS.min' => 'The TOKEN_EXPIRATION_DAYS must be at least 1.',
            'MAX_CODE_COUNT.min' => 'The MAX_CODE_COUNT must be at least 1.',
            'REFRESH_CODE_LIMIT.min' => 'The REFRESH_CODE_LIMIT must be at least 1.',
            'MAX_ACTIVE_TOKENS.integer' => 'The MAX_ACTIVE_TOKENS must be an integer.',
            'TOKEN_EXPIRATION_DAYS.integer' => 'The TOKEN_EXPIRATION_DAYS must be an integer.',
            'MAX_CODE_COUNT.integer' => 'The MAX_CODE_COUNT must be an integer.',
            'REFRESH_CODE_LIMIT.integer' => 'The REFRESH_CODE_LIMIT must be an integer.',
        ];
    }
}
