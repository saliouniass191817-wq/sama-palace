<?php

namespace App\Http\Requests\Admin;

use App\Models\Shop;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManageShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $shop = $this->route('shop');
        $shopId = $shop instanceof Shop ? $shop->id : null;

        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('shops', 'user_id')->ignore($shopId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops', 'slug')->ignore($shopId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
