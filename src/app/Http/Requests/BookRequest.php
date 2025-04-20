<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            case 'book.store' :
                return $this->bookStoreRules();
            case 'book.update' :
                return $this->bookUpdateRules();
            case 'books.get' :
                return $this->getBooksRules();
            case 'books.search' :
                return $this->searchBooksRules();
            default:
                return [];
        }
    }

    /**
     * @return array[]
     */
    private function bookStoreRules(): array
    {
        return [
            'author_id' => 'required|exists:authors,id',
            'title' => 'required|string|max:255',
            'isbn' => ['required', 'string', 'unique:books,isbn', 'regex:/^(?:\d{10}|\d{13})$/'],
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y')
        ];
    }

    /**
     * @return array[]
     */
    private function bookUpdateRules(): array
    {
        return [
            'author_id' => 'sometimes|required|exists:authors,id',
            'title' => 'sometimes|required|string|max:255',
            'isbn' => ['sometimes', 'required', 'string', 'unique:books,isbn,' . $this->book, 'regex:/^(?:\d{10}|\d{13})$/'],
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y')
        ];
    }

    /**
     * @return array[]
     */
    private function getBooksRules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y')
        ];
    }

    /**
     * @return array[]
     */
    private function searchBooksRules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'query' => ['nullable', 'string', 'max:255']
        ];
    }
}
