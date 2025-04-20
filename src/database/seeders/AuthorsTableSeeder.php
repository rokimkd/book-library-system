<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{
    public function run()
    {
        Author::factory()
            ->count(1000)
            ->hasBooks(rand(1, 5))
            ->create();
    }
}
