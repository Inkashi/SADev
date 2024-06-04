<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\DTO\PermissionDTO;

class CreatePermissionRequest extends FormRequest
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
            'name' => 'required|unique:Permissions',
            'code' => 'required|unique:Permissions',
        ];
    }

    public function createDTO(): PermissionDTO
    {
        return new PermissionDTO(
            $this->input('name'),
            $this->input('description'),
            $this->input('code'),
            $this->input('deleted_by'),
            $this->input('created_by')
        );
    }
}
