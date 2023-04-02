<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1_0;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema()
 */
final class PostCommentStoreRequest extends FormRequest
{
    /**
     * Comment
     * @OA\Property(
     *  maxLength=100,
     *  example="Comment"
     * )
     */
    public string $comment;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string'],
        ];
    }
}
