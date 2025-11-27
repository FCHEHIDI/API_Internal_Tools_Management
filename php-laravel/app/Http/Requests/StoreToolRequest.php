<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreToolRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:2|max:100|unique:tools,name',
            'description' => 'nullable|string',
            'vendor' => 'required|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'monthly_cost' => 'required|numeric|min:0|decimal:0,2',
            'active_users_count' => 'nullable|integer|min:0',
            'owner_department' => 'required|in:Engineering,Sales,Marketing,HR,Finance,Operations,Design',
            'status' => 'nullable|in:active,deprecated,trial'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tool name is required',
            'name.unique' => 'A tool with this name already exists',
            'name.min' => 'Tool name must be at least 2 characters',
            'name.max' => 'Tool name must not exceed 100 characters',
            'vendor.required' => 'Vendor name is required',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'monthly_cost.required' => 'Monthly cost is required',
            'monthly_cost.min' => 'Monthly cost must be a positive number',
            'monthly_cost.decimal' => 'Monthly cost must have at most 2 decimal places',
            'owner_department.required' => 'Owner department is required',
            'owner_department.in' => 'Invalid department. Must be one of: Engineering, Sales, Marketing, HR, Finance, Operations, Design',
            'website_url.url' => 'Website URL must be a valid URL format'
        ];
    }
}
