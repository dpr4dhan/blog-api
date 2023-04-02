<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */
final class CommentResource extends JsonResource
{
    /**
     * Uuid
     * @var string
     * @OA\Property( description="Uuid")
     */
    public ?string $uuid;
    /**
     * Comment
     * @var string
     * @OA\Property( description="Comment")
     */
    public ?string $comment;

    /**
     * Commentator
     * @var UserResource
     * @OA\Property( description="Commentator")
     */
    public ?UserResource $commentator;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'uuid'       => $this['uuid'],
            'comment'      => $this['comment'],
            'commentator'     => $this->whenLoaded('commentator', UserResource::make($this['commentator'])),
        ];
    }
}
