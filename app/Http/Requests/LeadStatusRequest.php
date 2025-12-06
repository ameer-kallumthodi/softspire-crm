<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('leadStatus') ? $this->route('leadStatus')->id : null;
        
        return [
            'name' => 'required|string|max:255|unique:lead_statuses,name,' . $id,
            'status' => 'required|string|in:active,inactive',
        ];
    }
}

