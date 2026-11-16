<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManageUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone');

        if (is_string($phone)) {
            $this->merge(['phone' => preg_replace('/[\s().-]+/', '', $phone)]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : null;

        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:30',
                'regex:/^\+[1-9][0-9]{7,14}$/',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'adress' => ['required', 'string', 'max:255'],
            'password' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'string',
                Password::min(8),
            ],
        ];
    }
}
