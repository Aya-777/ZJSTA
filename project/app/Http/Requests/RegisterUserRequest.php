<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'first_name'=>'required|string|max:255|min:3',
            'last_name'=>'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password'=>'required|string|min:8|confirmed',
            'phone_number'=>'required|string|unique:users,phone_number',
            'role'=>'required|string|in:owner,renter',
            // 'profile_picture'=>'nullable|string',
            // 'identity_image'=>'nullable|string',
            'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'identity_image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'birth_date'=>'required|date'
        ];
    }
}
