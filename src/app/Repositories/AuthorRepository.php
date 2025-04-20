<?php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use LaravelIdea\Helper\App\Models\_IH_Author_C;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function __construct(private Author $author) {}

    public function getAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->author->newQuery();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id)
    {
        $cached_author = $this->author->checkCache($id);

        if ($cached_author) {
            return json_decode($cached_author);
        }

        $author = $this->author->with('books')->find($id);

        if (!$author) {
            return null;
        }

        $author->setCache(
            id: $id,
            data: $author->toJson()
        );

        return $author;
    }

    public function create(array $data)
    {
        $author = $this->author->create($data);

        $authorWithBooks = $author->load('books');

        $author->setCache(
            id: $author->id,
            data: $authorWithBooks->toJson()
        );

        return $author;
    }

    public function update(int $id, array $data)
    {
        $author = $this->author->find($id);
        $author->update($data);

        $fresh_author = $author->fresh()->load('books');

        if ($author->checkCache($id)) {
            $author->deleteCache($id);
        }

        $author->setCache(
            id: $id,
            data: $fresh_author->toJson()
        );

        return $fresh_author;
    }

    public function delete(int $id): void
    {
        $this->author->destroy($id);

        if ($this->author->checkCache($id)) {
            $this->author->deleteCache($id);
        }
    }
}
