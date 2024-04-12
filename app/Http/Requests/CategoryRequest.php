<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'title'      => 'required|string|max:55',
            'meta_title' => 'string|max:55',
            'slug'       => 'required|string|max:55',
            'parent_id'  => 'integer'
        ];
    }
}
