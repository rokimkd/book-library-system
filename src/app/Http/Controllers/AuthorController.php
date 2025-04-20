<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function __construct(private AuthorService $authorService) {}

    public function index(AuthorRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $filters = $request->only(['name']);

        return $this->authorService->getAllAuthors($filters, $perPage);
    }

    public function store(AuthorRequest $request): JsonResponse
    {
        return $this->authorService->createAuthor($request->validated());
    }

    public function show(int $id): JsonResponse
    {
        return $this->authorService->getAuthorById($id);
    }

    public function update(AuthorRequest $request, int $id): JsonResponse
    {
        return $this->authorService->updateAuthor($id, $request->validated());
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->authorService->deleteAuthor($id);
    }
}
