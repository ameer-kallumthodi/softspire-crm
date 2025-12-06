<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'remarks' => 'required|string',
            'date' => 'required|date',
        ];
    }
}

