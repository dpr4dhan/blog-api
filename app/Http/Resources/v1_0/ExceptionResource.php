<?php

declare(strict_types=1);

namespace App\Http\Resources\v1_0;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */
final class ExceptionResource extends JsonResource
{
    /**
     * Code
     * @var string
     * @OA\Property( description="Code")
     */
    public string $code;
    /**
     * Message
     * @var string
     * @OA\Property( description="Message")
     */
    public string $message;

    public function toArray($request): array
    {
        return [
            'code' => $this['code'],
            'message' => $this['message'],
        ];
    }
}
