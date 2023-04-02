<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Api\AuthTokenRequest;
use App\Http\Resources\v1_0\ExceptionResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class AuthLogoutController.
 */
class AuthLogoutController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/v1/auth/logout",
     *      operationId="logoutUser",
     *      tags={"Auth"},
     *      summary="Logout User",
     *      description="Logout User",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(response=200, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#components/schemas/AuthLogoutResource")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="User unauthenticated"
     *         )
     * )
     */
    public function __invoke(): JsonResponse | ExceptionResource
    {
        try{
            $user = Auth::user();
            $user->currentAccessToken()->delete();
            return response()->json([
                'message' => $user->name. ' has been logout.',
            ], Response::HTTP_OK);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }
    }
}
