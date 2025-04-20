<?php

namespace App\Services;

use App\CustomResponseFormat;
use App\Http\Resources\BookIndexResource;
use App\Http\Resources\BookResource;
use App\Jobs\FetchBookCover;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository,
        private BookCoverService        $bookCoverService
    )
    {
    }

    public function getAllBooks(array $filters = [], int $perPage = 10): JsonResponse
    {
        $books = $this->bookRepository->getAll($filters, $perPage);
        $book_collection = BookIndexResource::collection($books);

        return CustomResponseFormat::successFormat(
            data: [
                'items' => $book_collection,
                'meta' => $this->getBooksMeta($book_collection)
            ]
        );
    }

    public function getBookById(int $id): JsonResponse
    {
        if (!$book = $this->bookRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Book not found');
        }

        return CustomResponseFormat::successFormat(data: new BookResource($book));
    }

    public function createBook(array $data): JsonResponse
    {
        $coverUrl = $this->bookCoverService->fetchCoverUrl($data['isbn']);
        if ($coverUrl) {
            $data['cover_url'] = $coverUrl;
        }

        $book = $this->bookRepository->create($data);

        if (!$coverUrl) {
            FetchBookCover::dispatch($book, $data['isbn'])
                ->delay(now()->addSeconds(10));
        }

        return CustomResponseFormat::successFormat(
            message: 'Book is created successfully',
            data: [
                'book_id' => $book->id
            ],
            status: 201
        );
    }

    public function updateBook(int $id, array $data): JsonResponse
    {
        if (!$this->bookRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Book not found');
        }

        if (isset($data['isbn'])) {
            $coverUrl = $this->bookCoverService->fetchCoverUrl($data['isbn']);
            if ($coverUrl) {
                $data['cover_url'] = $coverUrl;
            }
        }

        $book = $this->bookRepository->update($id, $data);

        if (isset($data['isbn']) && !isset($data['cover_url'])) {
            FetchBookCover::dispatch($book, $data['isbn']);

            return CustomResponseFormat::successFormat(
                message: 'Book updated successfully. Cover image will be processed shortly.'
            );
        }

        return CustomResponseFormat::successFormat(
            message: 'Book updated successfully.',
            data: [
                'book' => new BookResource($book)
            ]
        );
    }

    public function deleteBook(int $id): JsonResponse
    {
        if (!$this->bookRepository->findById($id)) {
            return CustomResponseFormat::badRequestFormat('Book not found');
        }

        $this->bookRepository->delete($id);

        return CustomResponseFormat::successFormat(
            message: 'Book with id ' . $id . ': is removed successfully'
        );
    }

    public function searchBooks(string $query, int $perPage = 10): JsonResponse
    {
        $books = $this->bookRepository->search($query, $perPage);

        $book_collection = BookIndexResource::collection($books);

        return CustomResponseFormat::successFormat(
            data: [
                'items' => $book_collection,
                'meta' => $this->getBooksMeta($book_collection)
            ]
        );
    }

    public function getBooksMeta(BookResource|AnonymousResourceCollection $resource): array
    {
        return [
            'current_page' => $resource->resource->currentPage(),
            'per_page' => $resource->resource->perPage(),
            'total' => $resource->resource->total(),
        ];
    }
}
