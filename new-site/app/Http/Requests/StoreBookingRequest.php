<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация формы брони (бывш. ручные проверки в fillSlot).
 * rims_with обязателен только для услуги №1 (riepu maiņa, riepas līdzi):
 * 1 — bez diskiem, 2 — ar diskiem.
 */
class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'position' => ['required', 'integer', 'min:1'],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('enabled', true)],
            'car_brand' => ['required', 'string', 'max:100'],
            'car_model' => ['required', 'string', 'max:100'],
            'license_plate' => ['required', 'string', 'max:20'],
            'rims_with' => ['required_if:service_id,1', 'nullable', Rule::in([1, 2])],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/'],
            'phone_country_code' => ['nullable', 'string', 'max:6'],
            'email' => ['nullable', 'email', 'max:255'],
            'customer_comment' => ['nullable', 'string', 'max:1000'],
            'is_mobile' => ['nullable', 'boolean'],
            'car_info' => ['nullable', 'array'],
            'car_info_vnr' => ['nullable', 'string', 'max:16'],
            'car_info_source' => ['nullable', 'string', 'max:32'],
        ];
    }

    public function messages(): array
    {
        return [
            'car_brand.required' => 'Ievadiet auto marku!',
            'car_model.required' => 'Ievadiet auto modeli!',
            'license_plate.required' => 'Ievadiet auto reģistrācijas numuru!',
            'service_id.required' => 'Jāizvēlas viens no pakalpojumiem!',
            'service_id.exists' => 'Jāizvēlas viens no pakalpojumiem!',
            'rims_with.required_if' => 'Jāizvēlas viena no opcijām!',
            'rims_with.in' => 'Jāizvēlas viena no opcijām!',
            'phone_number.required' => 'Ievadiet telefona numuru!',
            'phone_number.regex' => 'Ievadiet pareizu telefona numuru!',
            'email.email' => 'Ievadiet pareizu epasta adresi!',
        ];
    }
}
