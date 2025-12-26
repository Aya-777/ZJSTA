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
            'password'=>'required|string|min:8',
            'phone_number'=>'required|string|unique:users,phone_number',
            'role'=>'required|string|in:owner,renter',
            'profile_picture'=>'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'identity_image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'birth_date'=>'required|date'
        ];
    }

    public function messages(): array
{
    return [
        'first_name.required' => 'Please enter your first name.',
        'last_name.required' => 'Please enter your last name.',
        'email.required' => 'An email address is required to register.',
        'email.email' => 'Please provide a valid email address format.',
        'email.unique' => 'This email address is already registered. Please try to log in.',
        'password.required' => 'A password is required.',
        'password.min' => 'The password must be at least 8 characters long.',
        // 'password.confirmed' => 'The password confirmation does not match.', 
        'identity_image.required' => 'An identity image is required for verification.',
        'identity_image.image' => 'The identity verification must be a valid image file.',
    ];
}

}
