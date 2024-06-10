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
            'MAX_ACTIVE_TOKENS', 'TOKEN_EXPIRATION_DAYS', 'MAX_CODE_COUNT', 'REFRESH_CODE_LIMIT', 'MAX_CODE_TIME' => 'integer|min:1|nullable',
        ];
    }
}
