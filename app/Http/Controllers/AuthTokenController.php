<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Api\AuthTokenRequest;
use App\Http\Resources\v1_0\ExceptionResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class AuthTokenController.
 */
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title=L5_SWAGGER_TITLE,
 *      description=L5_SWAGGER_DESCRIPTION,
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 *

 */
final class AuthTokenController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      operationId="loginUser",
     *      tags={"Auth"},
     *      summary="Login User",
     *      description="Login User",
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="password", type="string"),
     *             )
     *         )
     *      ),
     *      @OA\Response(response=201, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#components/schemas/AuthResource")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Error in input"
     *         )
     * )
     */
    public function __invoke(AuthTokenRequest $request): JsonResponse | ExceptionResource
    {
        try{
            $data = $request->validated();

            $user = User::whereEmail($data['email'])->first();

            if (! Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => [(string) trans('validation.credentials')],
                ]);
            }

            return response()->json([
                'token' => $user
                    ->createToken('API Token')
                    ->plainTextToken,
            ], Response::HTTP_CREATED);
        }catch(\Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }
}
