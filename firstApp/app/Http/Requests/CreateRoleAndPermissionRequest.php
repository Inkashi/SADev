<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\DTO\RoleAndPermissionDTO;

class CreateRoleAndPermissionRequest extends FormRequest
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
        return [];
    }

    public function createDTO(): RoleAndPermissionDTO
    {
        return new RoleAndPermissionDTO(
            $this->input('role_id'),
            $this->input('permission_id'),
            $this->input('deleted_by'),
            $this->input('created_by')
        );
    }
}
