<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|string|unique:users,username|min:6|max:50',
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|string|confirmed|min:8|max:50',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'phone_number' => 'required|string|max:40',
            'address' => 'required|string|max:255',
        ];
    }
}
