<?php

declare(strict_types=1);

use App\Enums\Version;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

it('returns a not found error if it tries to call an invalid version', function () {
    $endpoint = routeVersioned('frontend', [], 'v0.1');

    actingAs()
        ->getJson($endpoint)
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('returns a successful status code', function (): void {
    Post::factory(3)->create();
    $endpoint = routeVersioned('frontend/posts', [], Version::v1_0);

    actingAs()
        ->getJson($endpoint)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
});

it('create like for the post', function(): void
{
    $post = Post::factory()->create();
    $endpoint = routeVersioned('frontend/posts/like', ['post' => $post], Version::v1_0);
    actingAs()
        ->postJson($endpoint)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'data' => [
                'uuid'       => $post->uuid,
                'title'      => $post->title,
                'body'       => $post->content,
                'status'     => $post->status,
                'publish_date' => $post->publish_date,
                'is_featured' => $post->is_featured,
                'created_at' => $post->created_at,
                'author'     => [
                    'uuid'       => $post->author->uuid,
                    'name'       => $post->author->name,
                    'email'      => $post->author->email,
                    'created_at' => $post->author->created_at,
                ],
                'total_likes' => $post->likes->count(),
                'comments' => $post->comments
            ],
        ]);
});

it('add new comment for the post', function(): void
{
    $post = Post::factory()->create();
    $endpoint = routeVersioned('frontend/posts/comment', ['post' => $post], Version::v1_0);
    actingAs()
        ->postJson($endpoint,[
            'comment' => 'Nice blog post bro'
        ])
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'data' => [
                'uuid'       => $post->uuid,
                'title'      => $post->title,
                'body'       => $post->content,
                'status'     => $post->status,
                'publish_date' => $post->publish_date,
                'is_featured' => $post->is_featured,
                'created_at' => $post->created_at,
                'author'     => [
                    'uuid'       => $post->author->uuid,
                    'name'       => $post->author->name,
                    'email'      => $post->author->email,
                    'created_at' => $post->author->created_at,
                ],
                'total_likes' => $post->likes->count(),
                'comments' => [
                    [
                        'comment'=> 'Nice blog post bro',
                        'commentator' => [
                            'name' => $post->comments[0]->commentator->name,
                            'email' => $post->comments[0]->commentator->email,
                            'uuid' => $post->comments[0]->commentator->uuid,
                            'created_at' => $post->comments[0]->commentator->created_at,
                        ],
                        'uuid'=> $post->comments[0]->uuid
                    ]
                ]
            ],
        ]);
});
