<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @mixin User
 */

/**
 * @OA\Schema()
 */
final class UserResource extends JsonResource
{

    /**
     * Uuid
     * @var string
     * @OA\Property( description="Uuid")
     */
    public string $uuid;
    /**
     * Name
     * @var string
     * @OA\Property( description="Name")
     */
    public string $name;
    /**
     * Email
     * @var string
     * @OA\Property( description="Email")
     */
    public string $email;
    /**
     * Created At
     * @var string
     * @OA\Property( description="Created At")
     */
    public string $created_at;

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
            'name'       => $this['name'],
            'email'      => $this['email'],
            'created_at' => $this['created_at'],
        ];
    }
}
