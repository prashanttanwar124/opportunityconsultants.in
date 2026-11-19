<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCounsellingRequest extends FormRequest
{
    /**
     * Scroll directly to the form card when validation fails.
     */
    protected $redirect = '/free-counselling#counselling-form';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Keep only the digits of the phone number.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\D/', '', (string) $this->input('phone')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $options = config('site.counselling');

        return [
            'level' => ['required', Rule::in(array_keys($options['levels']))],
            'country' => ['required', Rule::in(array_keys($options['countries']))],
            'intake' => ['required', Rule::in(array_keys($options['intakes']))],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'digits:10'],
            'email' => ['nullable', 'email', 'max:190'],
            'city' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'level.required' => 'Please tell us what you are looking for.',
            'country.required' => 'Please pick where you would like to go.',
            'intake.required' => 'Please tell us when you want to start.',
            'phone.digits' => 'Please enter a 10-digit WhatsApp number.',
            'consent.accepted' => 'Please tick the consent box so a counsellor can contact you.',
        ];
    }
}
