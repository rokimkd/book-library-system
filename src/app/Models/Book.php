<?php

namespace App\Models;

use App\Traits\RedisMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;
    use RedisMethods;

    protected $fillable = [
        'author_id',
        'title',
        'isbn',
        'description',
        'published_year',
        'cover_url'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
