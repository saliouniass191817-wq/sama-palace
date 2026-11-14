<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone');

        if (is_string($phone)) {
            $this->merge(['phone' => preg_replace('/[\s().-]+/', '', $phone)]);
        }
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:30', 'regex:/^\\+[1-9][0-9]{7,14}$/'],
            'password' => ['required', 'string'],
        ];
    }
}
