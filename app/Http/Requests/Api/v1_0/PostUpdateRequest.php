<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1_0;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema()
 */
final class PostUpdateRequest extends FormRequest
{
    /**
     * Title
     * @OA\Property(
     *  maxLength=100,
     *  example="Title"
     * )
     */
    public string $title;
    /**
     * Content
     * @OA\Property(
     *  example="Here goes the content"
     * )
     */
    public ?string $body;
    /**
     * Status
     * @OA\Property(
     *  example="draft or published or archived"
     * )
     */
    public ?string $status;
    /**
     * Is Featured
     * @OA\Property()
     */
    public ?bool $is_featured;

    /**
     * Publish Date
     * @OA\Property()
     */
    public ?string $publish_date;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', Rule::unique('posts')->ignore($this->id), 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['string', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['boolean'],
            'publish_date' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
