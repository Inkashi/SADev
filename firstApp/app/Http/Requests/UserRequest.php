<?php

namespace App\Http\Requests;

use App\DTO\UserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
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
            //
        ];
    }

    public function createDTO() : UserDTO {
        $user = $this->user();
        $DTO = new UserDTO(
            $user->id,
            $user->username,
            $user->email,
            $user->birthday,
            $user->created_at
        );

        $DTO->roles = $user->roles();

        foreach($DTO->roles as $role) {
            $DTO->permissions[$role->name] = $role->permissions();
        };

        return $DTO;
    }
}
