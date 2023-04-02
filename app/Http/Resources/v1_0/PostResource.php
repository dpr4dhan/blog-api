<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @OA\Schema()
 */
final class PostResource extends JsonResource
{
    /**
     * Uuid
     * @var string
     * @OA\Property( description="Uuid")
     */
    public ?string $uuid;
    /**
     * Title
     * @var string
     * @OA\Property( description="Title")
     */
    public ?string $title;

    /**
     * Body
     * @var string
     * @OA\Property( description="Body")
     */
    public ?string $body;
    /**
     * Status
     * @var string
     * @OA\Property( description="Status")
     */
    public ?string $status;
    /**
     * Publish Date
     * @var string
     * @OA\Property( description="Publish Date")
     */
    public ?string $publish_date;
    /**
     * Created At
     * @var string
     * @OA\Property( description="Created At")
     */
    public ?string $created_at;
    /**
     * Is Featured
     * @var string
     * @OA\Property( description="Is Featured")
     */
    public ?string $is_featured;
    /**
     * Author
     * @var UserResource
     * @OA\Property( description="Author")
     */
    public ?UserResource $author;
    /**
     * Total Likes
     * @var int
     * @OA\Property(description="Total Likes")
     */
    public ?int $total_likes;
    /**
     * Comments
     * @var Collection
     * @OA\Property(description="Comments")
     */
    public ?Collection $comments;



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
            'title'      => $this['title'],
            'body'       => $this['content'],
            'status'     => $this['status'],
            'publish_date' => $this['publish_date'],
            'created_at' => $this['created_at'],
            'is_featured' => $this['is_featured'],
            'author'     => $this->whenLoaded('author', UserResource::make($this['author'])),
            'total_likes' => $this['likes']->count(),
            'comments' => $this['comments'] ? $this->whenLoaded('comments', CommentResource::collection($this['comments'])) : null
        ];
    }
}
