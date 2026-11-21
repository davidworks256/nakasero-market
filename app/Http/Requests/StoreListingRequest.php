<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isFarmer() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:20'],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'quantity_available' => ['required', 'integer', 'min:0', 'max:1000000'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,sold_out'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required' => 'Enter the price in UGX (no commas).',
            'quantity_available.required' => 'Enter how much you have available.',
            'photos.*.image' => 'Each photo must be a jpg, png or webp image.',
            'photos.*.max' => 'Each photo must be 2 MB or smaller.',
        ];
    }
}
