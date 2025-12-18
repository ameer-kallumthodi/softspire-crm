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
        $rules = [
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'remarks' => 'required|string',
            'date' => 'required|date',
            'needed_followup' => 'sometimes|boolean',
        ];

        // If followup is needed, followup_date is required and must be today or future
        if ($this->input('needed_followup') == '1' || $this->input('needed_followup') === true) {
            $rules['followup_date'] = 'required|date|after_or_equal:today';
        }

        return $rules;
    }
}

