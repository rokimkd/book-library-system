<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AuthorResource extends JsonResource
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
            'name' => $this->name,
            'biography' => $this->biography ?? null,
            'birth_date' => Carbon::parse($this->birth_date)->format('Y-m-d') ?? null,
            'books' => BookResource::collection($this->books),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d h:i A'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d h:i A')
        ];
    }
}
