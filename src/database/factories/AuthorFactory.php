<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'biography' => $this->faker->paragraphs(3, true),
            'birth_date' => $this->faker->date(),
        ];
    }

    public function withBooks($count = 1)
    {
        return $this->afterCreating(function ($author) use ($count) {
            Book::factory()->count($count)->create(['author_id' => $author->id]);
        });
    }
}
