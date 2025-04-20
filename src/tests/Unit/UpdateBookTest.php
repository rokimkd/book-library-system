<?php

namespace Tests\Unit;

use App\Jobs\FetchBookCover;
use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\BookCoverService;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class UpdateBookTest extends TestCase
{
    use RefreshDatabase;

    private BookRepositoryInterface $bookRepository;
    private BookCoverService $bookCoverService;
    private BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->bookRepository = Mockery::mock(BookRepositoryInterface::class);
        $this->bookCoverService = Mockery::mock(BookCoverService::class);
        $this->bookService = new BookService($this->bookRepository, $this->bookCoverService);
    }

    public function testUpdateBookDispatchesCoverFetchJobWhenIsbnProvidedWithoutCoverUrl(): void
    {
        $book = Book::factory()->create();
        $updateData = [
            'isbn' => '9780618919420',
            'title' => 'Updated Title'
        ];

        $this->bookRepository->shouldReceive('findById')
            ->once()
            ->with($book->id)
            ->andReturn($book);

        $this->bookCoverService->shouldReceive('fetchCoverUrl')
            ->once()
            ->with($updateData['isbn'])
            ->andReturn(null);

        $this->bookRepository->shouldReceive('update')
            ->once()
            ->with($book->id, $updateData)
            ->andReturn($book);

        $response = $this->bookService->updateBook($book->id, $updateData);
        Queue::assertPushed(FetchBookCover::class, function ($job) use ($book, $updateData) {
            return $job->book->id === $book->id && $job->isbn === $updateData['isbn'];
        });

        $this->assertEquals(
            'Book updated successfully. Cover image will be processed shortly.',
            $response->getData()->message
        );
    }
}
