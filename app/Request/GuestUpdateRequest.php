<?php

namespace App\Request;

use Core\Valid\Form;

class GuestUpdateRequest extends Form
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'str', 'trim', 'min:1', 'max:100'],
            'greeting' => ['nullable', 'str', 'trim', 'min:1', 'max:100'],
            'category' => ['nullable', 'str', 'trim', 'min:1', 'max:50'],
            'presence' => ['nullable', 'bool'],
        ];
    }
}