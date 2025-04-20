<?php

namespace Tests\Integration;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_authors()
    {
        Author::factory()->count(3)->create();

        $response = $this->getJson('/authors/get');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => ['name', 'created_at', 'updated_at']
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

    public function test_can_create_author()
    {
        $authorData = [
            'name' => 'John Doe',
            'biography' => 'A test biography',
            'birth_date' => '1980-01-01',
        ];

        $response = $this->postJson('/authors/store', $authorData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'author_id',
                ],
                'message',
                'status',
                'type'
            ]);
    }

    public function test_can_show_author()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/authors/get/{$author->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'biography',
                    'birth_date',
                    'books',
                    'created_at',
                    'updated_at'
                ],
                'message',
                'status',
                'type'
            ]);
    }

    public function test_can_update_author()
    {
        $author = Author::factory()->create();
        $updatedData = ['name' => 'Updated Name'];

        $response = $this->patchJson("/authors/update/{$author->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'data' =>
                    [
                        'author' => ['name' => 'Updated Name']
                    ]
            ]);
    }

    public function test_can_delete_author()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/authors/delete/{$author->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    }
}
