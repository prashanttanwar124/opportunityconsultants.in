<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCallbackRequest extends FormRequest
{
    /**
     * Where to send the visitor back to when validation fails.
     */
    protected $redirect = '/#callback';

    /**
     * The error bag, so callback errors don't collide with other forms.
     */
    protected $errorBag = 'callback';

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
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'digits_between:10,15'],
            'interest' => ['required', Rule::in(array_keys(config('site.counselling.interests')))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.digits_between' => 'Please enter a valid WhatsApp number (at least 10 digits).',
        ];
    }
}
