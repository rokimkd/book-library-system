<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param object $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray(object $request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'description' => $this->description ?? null,
            'published_year' => $this->published_year ?? null,
            'cover_url' => $this->cover_url ?? null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d h:i A'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d h:i A')
        ];
    }
}
