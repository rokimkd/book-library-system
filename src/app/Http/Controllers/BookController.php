<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(private BookService $bookService) {}

    public function index(BookRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $filters = $request->only(['title', 'author_id', 'published_year']);

        return $this->bookService->getAllBooks($filters, $perPage);
    }

    public function store(BookRequest $request): JsonResponse
    {
        return $this->bookService->createBook($request->validated());
    }

    public function show(int $id): JsonResponse
    {
        return $this->bookService->getBookById($id);
    }

    public function update(BookRequest $request, int $id): JsonResponse
    {
        return $this->bookService->updateBook($id, $request->validated());
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->bookService->deleteBook($id);
    }

    public function search(BookRequest $request): JsonResponse
    {
        $query = $request->get('query', '');
        $perPage = $request->get('per_page', 10);

        return $this->bookService->searchBooks($query, $perPage);
    }
}
