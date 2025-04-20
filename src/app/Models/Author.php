<?php

namespace App\Models;

use App\Traits\RedisMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;
    use RedisMethods;

    protected $fillable = [
        'name',
        'biography',
        'birth_date'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
