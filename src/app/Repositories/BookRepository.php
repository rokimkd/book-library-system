<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(private Book $book) {}

    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->book;

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        if (!empty($filters['published_year'])) {
            $query->where('published_year', $filters['published_year']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        $cached_book = $this->book->checkCache($id);

        if ($cached_book) {
            return json_decode($cached_book);
        }

        $book = $this->book->find($id);

        if (!$book) {
            return null;
        }

        $book->setCache(
            id: $id,
            data: $book->toJson()
        );

        return $book;
    }

    public function create(array $data)
    {
        $book = $this->book->create($data);

        $book->setCache(
            id: $book->id,
            data: $book->toJson()
        );

        return $book;
    }

    public function update(int $id, array $data)
    {
        $book = $this->book->find($id);
        $book->update($data);

        if ($book->checkCache($id)) {
            $book->deleteCache($id);
        }

        $book->setCache(
            id: $id,
            data: $book->toJson()
        );

        return $book;
    }

    public function delete(int $id): void
    {
        $this->book->destroy($id);

        if ($this->book->checkCache($id)) {
            $this->book->deleteCache($id);
        }
    }

    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->book->with('author')
            ->where('title', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->orWhereHas('author', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->paginate($perPage);
    }
}
