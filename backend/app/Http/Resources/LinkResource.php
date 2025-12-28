<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Link",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="original_url", type="string", example="https://example.com/very/long/url"),
 *     @OA\Property(property="slug", type="string", example="abc123"),
 *     @OA\Property(property="short_url", type="string", example="http://localhost:8000/abc123"),
 *     @OA\Property(property="title", type="string", nullable=true, example="My Link"),
 *     @OA\Property(property="access_count", type="integer", example=42),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class LinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_url' => $this->original_url,
            'slug' => $this->slug,
            'short_url' => $this->short_url,
            'title' => $this->title,
            'access_count' => $this->access_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
