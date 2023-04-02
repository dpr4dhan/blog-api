<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Frontend;

use App\Exceptions\GeneralException;
use App\Http\Requests\Api\v1_0\PostStoreRequest;
use App\Http\Requests\Api\v1_0\PostUpdateRequest;
use App\Http\Resources\v1_0\ExceptionResource;
use App\Models\PostComments;
use App\Models\PostLikes;
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
 * Class FrontendController.
 */
class FrontendController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/frontend/posts",
     *      operationId="getAllPostPaginationList",
     *      tags={"Frontend"},
     *      summary="Get list of all posts",
     *      description="Returns list of all posts",
     *      @OA\Parameter(name="is_featured", in="query", description="Is Featured", required=false,
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
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
     *              @OA\Schema(
     *                  @OA\Property(property="resCod", type="string", example="200"),
     *                  @OA\Property(property="resDesc", type="string", example="Post List"),
     *                  @OA\Property(property="data", ref="#components/schemas/PostPaginationResponseDto")
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
     *     )
     */
    public function index(Request $request, Version $version): AnonymousResourceCollection
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
            $posts = Post::when($request->search, fn($query, $term) => $query->where('title' , 'like', '%'.$term.'%'))
                ->when($request->is_featured, fn($query, $term) => $query->where('is_featured' , $term == 'true' ? 1 : 0))
                ->orderBy($orderBy,$order)
                ->paginate($limit,['*'],'page', $page);
            return PostResource::collection($posts);
        }catch(Exception $ex){
            return $ex;
        }

    }

    /**
     * @OA\Post(
     *      path="/api/v1/frontend/posts/like/{post_id}",
     *      operationId="likePost",
     *      tags={"Frontend"},
     *      summary="Like post",
     *      description="Like post",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(name="post_id", in="path", description="Post Id", required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(response=200, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="resCod", type="string", example="200"),
     *                  @OA\Property(property="resDesc", type="string", example="Post List"),
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
     *     )
     */
    public function likePost(Request $request, Version $version, Post $post): JsonResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{
            // check if user has already liked the post
            $postLike = PostLikes::where('user_id', auth()->user()->id)->where('post_id', $post->id)->first();
            if($postLike){
                return ExceptionResource::make(['message'=> 'User has already liked the post', 'code'=>400]);
            }else{
                PostLikes::create([
                   'post_id' => $post->id,
                   'user_id' => auth()->user()->id
                ]);
                return PostResource::make($post->refresh());
            }
        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/frontend/posts/comment/{post_id}",
     *      operationId="commentPost",
     *      tags={"Frontend"},
     *      summary="Comment on post",
     *      description="Comment on post",
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(name="post_id", in="path", description="Post Id", required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="application/json",
     *               @OA\Schema(ref="#/components/schemas/PostCommentStoreRequest")
     *         )
     *      ),
     *      @OA\Response(response=200, description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="resCod", type="string", example="200"),
     *                  @OA\Property(property="resDesc", type="string", example="Post List"),
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
     *     )
     */
    public function commentPost(Request $request, Version $version, Post $post): JsonResource
    {
        abort_unless(
            $version->greaterThanOrEqualsTo(Version::v1_0),
            Response::HTTP_NOT_FOUND
        );
        try{

            PostComments::create([
                'post_id' => $post->id,
                'user_id' => auth()->user()->id,
                'comment' => $request->comment
            ]);
            return PostResource::make($post->refresh());

        }catch(Exception $ex){
            return ExceptionResource::make(['message'=> $ex->getMessage(), 'code'=>$ex->getCode()]);
        }
    }
}
