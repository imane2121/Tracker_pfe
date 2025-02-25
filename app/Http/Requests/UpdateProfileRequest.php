<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateProfileRequest extends FormRequest
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
        return [
            'first_name' => ['required', 'string', 'max:255'], // Validate first name
            'last_name'  => ['required', 'string', 'max:255'], // Validate last name
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()], // Validate email
        ];
    }
}