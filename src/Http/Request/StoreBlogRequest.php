<?php

namespace atikullahnasar\blog\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->id;
        return [
            'title' => [ 'required', 'string', 'max:255', Rule::unique('blogs', 'title')->ignore($id)],
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'show_home' => 'boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('published_at', 'required', function ($input) {
            return $input->status === 'published';
        });
    }
    public function prepareForValidation(): void
    {
        $this->merge([
            'show_home' => (bool) $this->input('show_home', false), // default false
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The blog title is required.',
            'content.required' => 'The blog content is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'featured_image.image' => 'The file must be an image.',
            'featured_image.max' => 'The image size should not exceed 2MB.',
        ];
    }
}
