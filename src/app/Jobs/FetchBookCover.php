<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\BookCoverService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchBookCover implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Book $book,
        public string $isbn
    ) {}

    public function handle(BookCoverService $bookCoverService): void
    {
        try {
            $coverUrl = $bookCoverService->fetchCoverUrl($this->isbn);

            if ($coverUrl) {
                $this->book->update(['cover_url' => $coverUrl]);
                Log::info("Successfully fetched cover for book {$this->book->id}");
            } else {
                Log::warning("No cover found for ISBN {$this->isbn}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch cover for book {$this->book->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed for book {$this->book->id}: " . $exception->getMessage());
    }
}
