<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1_0;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema()
 */

final class UserStoreRequest extends FormRequest
{
    /**
     * Name
     * @OA\Property(
     *  maxLength=100,
     *  example="Name"
     * )
     */
    public ?string $name;
    /**
     * Email
     * @OA\Property(
     *  maxLength=100,
     *  example="Email"
     * )
     */
    public ?string $email;
    /**
     * Password
     * @OA\Property(
     *  maxLength=100,
     *  example="Password"
     * )
     */
    public ?string $password;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', Password::min(8)->mixedCase()->symbols()->numbers()],
        ];
    }
}
