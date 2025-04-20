<?php

namespace Tests\Integration;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_books()
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/books/get');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => ['title', 'isbn', 'created_at', 'updated_at']
                    ],
                    'meta' => ['current_page', 'per_page', 'total']
                ],
                'message',
                'status',
                'type'
            ]);

        $response->assertJson([
            'status' => 200,
            'type' => 'general_success'
        ]);
    }

    public function test_can_create_book()
    {
        $author = Author::factory()->create();

        $bookData = [
            'author_id' => $author->id,
            'title' => 'Test Book',
            'isbn' => '1234567890123',
            'description' => 'A test book description',
            'published_year' => 2023,
        ];

        $response = $this->postJson('/books/store', $bookData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'book_id',
                ],
                'message',
                'status',
                'type'
            ]);
    }

    public function test_can_show_book()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/books/get/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'isbn',
                    'description',
                    'published_year',
                    'cover_url',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'status',
                'type'
            ]);
    }

    public function test_can_update_book()
    {
        $book = Book::factory()->create();

        $updatedData = ['isbn' => '1234567892'];

        $response = $this->patchJson("/books/update/{$book->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
                'message' => 'Book updated successfully. Cover image will be processed shortly.'
            ]);
    }

    public function test_can_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/books/delete/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_can_search_books()
    {
        Book::factory()->create(['title' => 'Unique Book Title']);

        $response = $this->getJson('/books/search?query=Unique');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Unique Book Title']);
    }
}
