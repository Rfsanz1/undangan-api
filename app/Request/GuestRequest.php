<?php

namespace App\Request;

use Core\Valid\Form;

class GuestRequest extends Form
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'str', 'trim', 'min:1', 'max:100'],
            'greeting' => ['nullable', 'str', 'trim', 'min:1', 'max:100'],
            'category' => ['nullable', 'str', 'trim', 'min:1', 'max:50'],
            'presence' => ['nullable', 'bool'],
        ];
    }
}