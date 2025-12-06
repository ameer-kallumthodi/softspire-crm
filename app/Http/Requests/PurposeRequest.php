<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurposeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('purpose') ? $this->route('purpose')->id : null;
        
        return [
            'name' => 'required|string|max:255|unique:purposes,name,' . $id,
            'status' => 'required|string|in:active,inactive',
        ];
    }
}

