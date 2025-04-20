<?php

namespace App\Services;

use App\CustomResponseFormat;
use App\Http\Resources\AuthorIndexResource;
use App\Http\Resources\AuthorResource;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository
    ) {}

    public function getAllAuthors(array $filters = [], int $perPage = 10): JsonResponse
    {
        $authors = $this->authorRepository->getAll($filters, $perPage);
        $author_collection = AuthorIndexResource::collection($authors);

        return CustomResponseFormat::successFormat(
            data: [
                'items' => $author_collection,
                'meta' => $this->getAuthorsMeta($author_collection)
            ]
        );
    }

    public function getAuthorById(int $id): JsonResponse
    {
        if (!$author = $this->authorRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Author not found');
        }

        return CustomResponseFormat::successFormat(data: new AuthorResource($author));
    }

    public function createAuthor(array $data): JsonResponse
    {
        $author = $this->authorRepository->create($data);

        return CustomResponseFormat::successFormat(
            message: 'Author is created successfully',
            data: [
                'author_id' => $author->id
            ],
            status: 201
        );
    }

    public function updateAuthor(int $id, array $data): JsonResponse
    {
        if (!$this->authorRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Author not found');
        }

        $author = $this->authorRepository->update($id, $data);

        return CustomResponseFormat::successFormat(
            message: 'Author is updated successfully',
            data: [
                'author' => new AuthorResource($author)
            ]
        );
    }

    public function deleteAuthor(int $id): JsonResponse
    {
        if (!$this->authorRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Author not found');
        }

        $this->authorRepository->delete($id);

        return CustomResponseFormat::successFormat(
            message: 'Author with id '. $id . ': is removed successfully'
        );
    }

    public function getAuthorsMeta(AuthorResource|AnonymousResourceCollection $resource): array
    {
        return [
            'current_page' => $resource->resource->currentPage(),
            'per_page' => $resource->resource->perPage(),
            'total' => $resource->resource->total(),
        ];
    }
}
