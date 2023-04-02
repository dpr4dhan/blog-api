<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */
final class PostPaginationResource extends JsonResource
{

    /**
     * Data
     * @var PostResource
     * @OA\Property( description="Post")
     */
    public string $data;
    /**
     * Meta
     * @var object
     * @OA\Property( description="meta")
     */
    public object $meta;
    /**
     * Links
     * @var object
     * @OA\Property( description="meta")
     */
    public object $links;

}
