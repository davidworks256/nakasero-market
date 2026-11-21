<?php

namespace App\Http\Requests;

class UpdateListingRequest extends StoreListingRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:listing_images,id'],
        ]);
    }
}
