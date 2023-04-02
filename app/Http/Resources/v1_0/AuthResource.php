<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */
final class AuthResource extends JsonResource
{
    /**
     * Token
     * @var string
     * @OA\Property( description="Token")
     */
    public string $token;

    public function toArray($token): array
    {
        return [
            'token' => $token,
        ];
    }
}
