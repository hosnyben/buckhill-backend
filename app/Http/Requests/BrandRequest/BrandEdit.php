<?php

namespace App\Http\Requests\BrandRequest;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BrandEdit extends FormRequest
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
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', // This line ensures the slug format
                Rule::unique('brands', 'slug')->ignore($this->brand->id),
            ]
        ];
    }
}
