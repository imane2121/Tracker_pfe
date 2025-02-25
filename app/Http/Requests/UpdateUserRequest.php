<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Allow all authenticated users to update their profile
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->route('user')->id; // Get the user ID from the route

        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId), // Ensure email is unique, ignoring the current user
            ],
            'role' => [
                'required',
                'string',
                'in:admin,contributor,supervisor', // Validate role
            ],
            'city_id' => [
                'nullable',
                'exists:cities,id', // Validate city_id if provided
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed', // Validate password confirmation
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email address is already in use.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The selected role is invalid.',
            'city_id.exists' => 'The selected city is invalid.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}