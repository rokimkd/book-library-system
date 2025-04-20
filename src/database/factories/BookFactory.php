<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition()
    {
        return [
            'author_id' => Author::factory(),
            'title' => $this->generateBookTitle(),
            'isbn' => $this->faker->unique()->isbn13,
            'description' => $this->faker->paragraphs(3, true),
            'published_year' => $this->faker->year,
            'cover_url' => 'https://covers.openlibrary.org/b/isbn/' . $this->faker->isbn13 . '-M.jpg',
        ];
    }

    private function generateBookTitle(): string
    {
        $prefixes = ['The', 'A', 'My', 'His', 'Her', 'Our', 'Their'];
        $nouns = ['Journey', 'Adventure', 'Life', 'Story', 'Secret', 'Mystery', 'Dream'];
        $adjectives = ['Great', 'Last', 'First', 'Final', 'Lost', 'Hidden', 'Forgotten'];
        $suffixes = ['of Time', 'of Destiny', 'of Dreams', 'of the Past', 'of the Future', 'of the Unknown'];

        $title = '';

        if ($this->faker->boolean(70)) {
            $title .= $this->faker->randomElement($prefixes) . ' ';
        }

        if ($this->faker->boolean(60)) {
            $title .= $this->faker->randomElement($adjectives) . ' ';
        }

        $title .= $this->faker->randomElement($nouns);

        if ($this->faker->boolean(50)) {
            $title .= ' ' . $this->faker->randomElement($suffixes);
        }

        return $title;
    }
}
