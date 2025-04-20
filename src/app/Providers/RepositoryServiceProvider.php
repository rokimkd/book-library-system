<?php

namespace App\Providers;

use App\Repositories\BookRepository;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\AuthorRepository;
use App\Repositories\Contracts\AuthorRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthorRepositoryInterface::class, function ($app) {
            return new AuthorRepository($app->make(\App\Models\Author::class));
        });

        $this->app->bind(BookRepositoryInterface::class, function ($app) {
            return new BookRepository($app->make(\App\Models\Book::class));
        });
    }

    public function boot()
    {
        //
    }
}
