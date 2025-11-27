<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateToolRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $toolId = $this->route('tool');
        
        return [
            'name' => 'sometimes|string|min:2|max:100|unique:tools,name,' . $toolId,
            'description' => 'nullable|string',
            'vendor' => 'sometimes|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'monthly_cost' => 'sometimes|numeric|min:0|decimal:0,2',
            'active_users_count' => 'nullable|integer|min:0',
            'owner_department' => 'sometimes|in:Engineering,Sales,Marketing,HR,Finance,Operations,Design',
            'status' => 'sometimes|in:active,deprecated,trial'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A tool with this name already exists',
            'name.min' => 'Tool name must be at least 2 characters',
            'name.max' => 'Tool name must not exceed 100 characters',
            'category_id.exists' => 'Selected category does not exist',
            'monthly_cost.min' => 'Monthly cost must be a positive number',
            'monthly_cost.decimal' => 'Monthly cost must have at most 2 decimal places',
            'owner_department.in' => 'Invalid department. Must be one of: Engineering, Sales, Marketing, HR, Finance, Operations, Design',
            'website_url.url' => 'Website URL must be a valid URL format'
        ];
    }
}
