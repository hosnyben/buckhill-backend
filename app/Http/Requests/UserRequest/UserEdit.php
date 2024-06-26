<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserEdit extends FormRequest
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
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => [
                'email',
                Rule::unique('users', 'email')->ignore(auth()->user()->id),
            ],
            'password' => 'string|min:8',
            'password_confirmation' => 'string|same:password',
            'avatar_uuid' => 'uuid|exists:files,uuid',
            'address' => 'string|max:255',
            'phone_number' => 'string|max:255',
            'is_marketing' => 'boolean'
        ];
    }
}
