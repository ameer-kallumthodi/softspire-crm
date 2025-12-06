<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('lead') ? $this->route('lead')->id : null;
        $countryCode = $this->input('country_code');
        $phone = $this->input('phone');
        
        return [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($countryCode, $phone, $id) {
                    $exists = \App\Models\Lead::where('country_code', $countryCode)
                        ->where('phone', $phone)
                        ->when($id, function($q) use ($id) {
                            return $q->where('id', '!=', $id);
                        })
                        ->exists();
                    if ($exists) {
                        $fail('The phone number with this country code already exists.');
                    }
                },
            ],
            'country_id' => 'required|exists:countries,id',
            'purpose_id' => 'required|exists:purposes,id',
            'lead_status_id' => 'required|exists:lead_statuses,id',
            'lead_source_id' => 'required|exists:lead_sources,id',
            'is_meta' => 'nullable|boolean',
            'meta_lead_id' => 'nullable|integer',
            'telecaller_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email|max:255',
            'is_converted' => 'nullable|boolean',
            'followup_date' => 'nullable|date',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
        ];
    }
}

