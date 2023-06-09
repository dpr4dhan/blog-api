<?php

declare(strict_types=1);

use App\Enums\Version;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

it('returns a not found error if it tries to call an invalid version', function () {
    $endpoint = routeVersioned('posts', ['post' => 1], 'v0.1');

    actingAs()
        ->getJson($endpoint)
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('shows the given post', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->getJson($endpoint)
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
