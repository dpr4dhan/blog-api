<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */
final class AuthLogoutResource extends JsonResource
{
    /**
     * Message
     * @var string
     * @OA\Property( description="Message")
     */
    public string $message;

    public function toArray($token): array
    {
        return [
            'message' => $message,
        ];
    }
}
