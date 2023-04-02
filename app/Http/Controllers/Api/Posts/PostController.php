<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Posts;

use App\Http\Requests\Api\v1_0\PostStoreRequest;
use App\Http\Requests\Api\v1_0\PostUpdateRequest;
use App\Http\Resources\v1_0\ExceptionResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\Version;
use App\Http\Resources\v1_0\PostResource;
use App\Models\Post;

/**
 * Class PostController.
 */
class PostController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/posts",
     *      operationId="getPostPaginationList",
     *      tags={"Post"},
     *      summary="Get list of posts of logged user",
     *      description="Returns list of posts of logged user",
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
     *              @OA\Schema(ref="#components/schemas/PostPaginationResource")
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
            $posts = Post::where('user_id', auth()->user()->id)
                ->when($request->search, fn($query, $term) => $query->where('title' , 'like', '%'.$term.'%'))
                ->orderBy($orderBy,$order)
                ->paginate($limit,['*'],'page', $page);
            return PostResource::collection($posts);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }

    /**
     * @OA\Post(
     *      path="/api/v1/posts",
     *      operationId="Create new Post",
     *      tags={"Post"},
     *      summary="Create new Post",
     *      description="Create new Post",
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="application/json",
     *               @OA\Schema(ref="#/components/schemas/PostStoreRequest")
     *         )
     *      ),
     *      @OA\Response(response=201, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#components/schemas/PostResource")
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
    public function store(PostStoreRequest $request, Version $version): JsonResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            $data = $request->validated();
            $post = $request->user()->posts()->create([
                'title' => $data['title'],
                'content' => $data['body'],
                'status' => $data['status'],
                'is_featured' => $data['is_featured'],
                'publish_date' => $data['publish_date']
            ]);

            return PostResource::make($post);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }

    /**
     * @OA\Get(
     *      path="/api/v1/posts/{id}",
     *      operationId="getPostById",
     *      tags={"Post"},
     *      summary="Get Post information",
     *      description="Get Post information",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
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
     *                  @OA\Property(property="data", ref="#components/schemas/PostResource")
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
    public function show(Request $request, Version $version, Post $post): JsonResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            return PostResource::make($post);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }

    /**
     * @OA\Patch(
     *      path="/api/v1/posts/{id}",
     *      operationId="updatePostById",
     *      tags={"Post"},
     *      summary="Update Post information",
     *      description="Update Post & return Post data",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="application/json",
     *               @OA\Schema(ref="#/components/schemas/PostUpdateRequest")
     *         )
     *      ),
     *      @OA\Response(response=200, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#components/schemas/PostResource")
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
    public function update(PostUpdateRequest $request, Version $version, Post $post): JsonResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            $data = $request->validated();
            $post->update([
                'title' => $data['title'],
                'content' => $data['body'],
                'status' => $data['status'] ?? $post->status,
                'is_featured' => $data['is_featured'] ?? $post->is_featured,
                'publish_date' => $data['publish_date'] ?? $post->publish_date
            ]);

            return PostResource::make($post->refresh());
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }

    /**
     * @OA\delete(
     *      path="/api/v1/posts/{id}",
     *      operationId="deletePost",
     *      tags={"Post"},
     *      summary="Delete Post",
     *      description="Delete Post",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Post id",
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
     *                  @OA\Property(property="message", type="string", example="Post deleted successfully"),
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
    public function destroy(Request $request, Version $version, Post $post): JsonResponse
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully',
            ], Response::HTTP_OK);
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }

    }
}
