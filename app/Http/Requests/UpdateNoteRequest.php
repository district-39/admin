<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'district_meeting_id' => ['nullable', 'exists:district_meetings,id'],
            'file_id' => ['nullable', 'exists:files,id'],
            'attendees' => ['nullable', 'array'],
            'attendees.*.user_id' => ['nullable', 'exists:users,id'],
            'attendees.*.email' => ['nullable', 'email', 'max:255'],
            'attendees.*.is_present' => ['required', 'boolean'],
            'attendees.*.is_gsr' => ['required', 'boolean'],
            'attendees.*.title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
