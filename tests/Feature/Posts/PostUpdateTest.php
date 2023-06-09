<?php

declare(strict_types=1);

use App\Enums\Version;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

it('returns a not found error if it tries to call an invalid version', function () {
    $endpoint = routeVersioned('posts', ['post' => 1], 'v0.1');

    actingAs()
        ->putJson($endpoint)
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('raises an error if title is not provided', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->putJson($endpoint, [
            'content' => 'the post content',
        ])
        ->assertJsonValidationErrorFor('title')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if body is not provided', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->putJson($endpoint, [
            'title' => 'the post title',
        ])
        ->assertJsonValidationErrorFor('body')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if status is not valid', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->putJson($endpoint, [
            'title' => 'the post title',
            'status'=> 'open'
        ])
        ->assertJsonValidationErrorFor('status')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if publish date is not valid', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->putJson($endpoint, [
            'title' => 'the post title',
            'status'=> 'open',
            'publish_date'=> '2023/08/08 12:45:45'
        ])
        ->assertJsonValidationErrorFor('publish_date')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('updates the given post', function (): void {
    $post = Post::factory()->create();
    $endpoint = routeVersioned('posts', ['post' => $post], Version::v1_0);

    actingAs()
        ->putJson($endpoint, [
            'title'   => 'The post title',
            'body' => 'The post content',
        ])
        ->assertSuccessful();

    $this->assertDatabaseMissing(Post::class, [
        'title'   => $post->title,
        'content' => $post->content,
    ]);

    $this->assertDatabaseHas(Post::class, [
        'id'      => $post->id,
        'title'   => 'The post title',
        'content' => 'The post content',
    ]);
});
