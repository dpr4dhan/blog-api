<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Requests\Api\v1_0\UserStoreRequest;
use App\Http\Requests\Api\v1_0\UserUpdateRequest;
use App\Http\Resources\v1_0\ExceptionResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Version;
use App\Http\Resources\v1_0\UserResource;
use App\Models\User;

/**
 * Class UserController.
 */

 class UserController extends Controller
{

    /**
      * @OA\Get(
      *      path="/api/v1/users",
      *      operationId="getUserPaginationList",
      *      tags={"User"},
      *      summary="Get list of users",
      *      description="Returns list of users",
      *      security={{"bearer_token":{}}},
      *      @OA\Parameter(name="search", in="query", description="Search", required=false,
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *     @OA\Parameter(name="page", in="query", description="Page (By default 1)", required=false,
      *          @OA\Schema(
      *              type="integer"
      *          )
      *      ),
      *      @OA\Parameter(name="limit", in="query", description="Limit (By default 10)", required=false,
      *          @OA\Schema(
      *              type="integer"
      *          )
      *      ),
      *      @OA\Parameter(name="orderBy", in="query", description="Order By Column", required=false,
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *      @OA\Parameter(name="order", in="query", description="Order (By default asc)", required=false,
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *      @OA\Response(response=200, description="successful operation",
      *          @OA\MediaType(
      *              mediaType="application/json",
      *              @OA\Schema(ref="#components/schemas/UserPaginationResource")
      *          )
      *      ),
      *      @OA\Response(
      *          response=403,
      *          description="Forbidden"
      *       ),
      *      @OA\Response(
      *          response=401,
      *          description="Unauthorized"
      *       ),
      *     )
      */
    public function index(Request $request, Version $version): AnonymousResourceCollection|ExceptionResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            $orderBy = $request->orderBy ??  'created_at';
            $order = $request->order ?? 'asc';
            $limit = $request->limit ?? '10';
            $page = $request->page ?? '1';
            $users = User::
                        when($request->search, fn($query, $term) => $query->where('name' , 'like', '%'.$term.'%'))
                        ->orderBy($orderBy,$order)
                        ->paginate($limit,['*'],'page', $page);
            return UserResource::collection($users);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }

     /**
      * @OA\Post(
      *      path="/api/v1/users",
      *      operationId="createNewUser",
      *      tags={"User"},
      *      summary="Register new User",
      *      description="Register new User",
      *      @OA\RequestBody(
      *          @OA\MediaType(mediaType="application/json",
      *               @OA\Schema(ref="#/components/schemas/UserStoreRequest")
      *         )
      *      ),
      *      @OA\Response(response=201, description="successful operation",
      *          @OA\MediaType(
      *              mediaType="application/json",
      *              @OA\Schema(
      *                  @OA\Property(property="data", ref="#components/schemas/UserResource")
      *              )
      *          )
      *      ),
      *      @OA\Response(
      *          response=422,
      *          description="Error in input"
      *      ),
      *      @OA\Response(
      *          response=403,
      *          description="Forbidden"
      *       ),
      *      @OA\Response(
      *          response=401,
      *          description="Unauthorized"
      *       ),
      * )
      */
     public function store(UserStoreRequest $request, Version $version): JsonResource
     {
         abort_unless(
             $version->greaterThanOrEqualsTo(Version::v1_0),
             Response::HTTP_NOT_FOUND
         );
         try{
             $data = $request->validated();
             $user = User::create([
                 'name' => $data['name'],
                 'email' => $data['email'],
                 'password' => Hash::make($data['password'])
             ]);
            return UserResource::make($user);
         }catch(Exception $ex){
             return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
         }

     }

     /**
      * @OA\Get(
      *      path="/api/v1/users/{id}",
      *      operationId="getUserById",
      *      tags={"User"},
      *      summary="Get User information",
      *      description="Get User information",
      *      security={{"bearer_token":{}}},
      *      @OA\Parameter(
      *          name="id",
      *          description="User id",
      *          required=true,
      *          in="path",
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *      @OA\Response(response=200, description="successful operation",
      *          @OA\MediaType(
      *              mediaType="application/json",
      *              @OA\Schema(
      *                  @OA\Property(property="data", ref="#components/schemas/UserResource")
      *              )
      *          )
      *      ),
      *      @OA\Response(
      *          response=403,
      *          description="Forbidden"
      *       ),
      *      @OA\Response(
      *          response=401,
      *          description="Unauthorized"
      *       ),
      * )
      */
     public function show(Request $request, Version $version, User $user): JsonResource
     {
         abort_unless(
             $version->greaterThanOrEqualsTo(Version::v1_0),
             Response::HTTP_NOT_FOUND
         );

         try{
             return UserResource::make($user);
         }catch (Exception $ex){
             return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
         }

     }

     /**
      * @OA\Patch(
      *      path="/api/v1/users/{id}",
      *      operationId="updateUserById",
      *      tags={"User"},
      *      summary="Update user information",
      *      description="Update user & return user data",
      *      security={{"bearer_token":{}}},
      *      @OA\Parameter(
      *          name="id",
      *          description="User id",
      *          required=true,
      *          in="path",
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *      @OA\RequestBody(
      *          @OA\MediaType(mediaType="application/json",
      *               @OA\Schema(ref="#/components/schemas/UserUpdateRequest")
      *         )
      *      ),
      *      @OA\Response(response=200, description="successful operation",
      *          @OA\MediaType(
      *              mediaType="application/json",
      *              @OA\Schema(
      *                  @OA\Property(property="data", ref="#components/schemas/UserResource")
      *              )
      *          )
      *      ),
      *      @OA\Response(
      *          response=422,
      *          description="Error in input"
      *      ),
      *      @OA\Response(
      *          response=403,
      *          description="Forbidden"
      *       ),
      *      @OA\Response(
      *          response=401,
      *          description="Unauthorized"
      *       ),
      * )
      */
     public function update(UserUpdateRequest $request, Version $version, User $user): JsonResource
     {
         abort_unless(
             $version->greaterThanOrEqualsTo(Version::v1_0),
             Response::HTTP_NOT_FOUND
         );

         try{
             $user->update($request->validated());

             return UserResource::make($user->refresh());
         }catch (Exception $ex){
             return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
         }

     }

     /**
      * @OA\delete(
      *      path="/api/v1/users/{id}",
      *      operationId="deleteUser",
      *      tags={"User"},
      *      summary="Delete User",
      *      description="Delete User",
      *      security={{"bearer_token":{}}},
      *      @OA\Parameter(
      *          name="id",
      *          description="User id",
      *          required=true,
      *          in="path",
      *          @OA\Schema(
      *              type="string"
      *          )
      *      ),
      *      @OA\Response(response=200, description="successful operation",
      *          @OA\MediaType(
      *              mediaType="application/json",
      *              @OA\Schema(
      *                  @OA\Property(property="message", type="string", example="User deleted successfully"),
      *              )
      *          )
      *      ),
      *      @OA\Response(
      *          response=403,
      *          description="Forbidden"
      *       ),
      *      @OA\Response(
      *          response=401,
      *          description="Unauthorized"
      *       ),
      * )
      */
     public function destroy(Request $request, Version $version, User $user): JsonResponse
     {
         abort_unless(
             $version->greaterThanOrEqualsTo(Version::v1_0),
             Response::HTTP_NOT_FOUND
         );
        try{
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully',
            ], Response::HTTP_OK);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

     }
}
