<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $route = $this->route()->getName();

        switch ($route) {
            case 'author.store' :
                return $this->getValidationRules('required');
            case 'author.update' :
                return $this->getValidationRules('sometimes|required');
            case 'authors.get' :
                return $this->getAuthorsRules();
            default:
                return [];
        }
    }

    /**
     * Get the common validation rules with specific name field rules
     *
     * @param string $name_rules The rules for the name field
     * @return array<string, string>
     */
    private function getValidationRules(string $name_rules): array
    {
        return [
            'name' => $name_rules . '|string|max:255',
            'biography' => 'nullable|string',
            'birth_date' => 'nullable|date'
        ];
    }

    /**
     * @return array[]
     */
    private function getAuthorsRules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
