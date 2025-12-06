<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('country') ? $this->route('country')->id : null;
        
        return [
            'name' => 'required|string|max:255|unique:countries,name,' . $id,
            'status' => 'required|string|in:active,inactive',
        ];
    }
}

