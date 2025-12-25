<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlsRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('crawl-urls');
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'urls'     => ['required', 'array', 'min:1'],
            'urls.*'   => ['required', 'url'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'urls.required'  => 'Please provide at least one URL.',
            'urls.array'     => 'The urls field must be an array.',
            'urls.*.required' => 'Each URL is required.',
            'urls.*.url'     => 'Please enter a valid URL.',
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'urls' => array_filter(array_map('trim', $this->input('urls', []))),
        ]);
    }
}
