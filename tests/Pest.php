<?php

declare(strict_types=1);

use Yard\LiveContent\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function editorScreen(string $postType): stdClass
{
	$screen = new stdClass();
	$screen->post_type = $postType;

	return $screen;
}

function editorPost(int $id): Mockery\MockInterface
{
	$post = Mockery::mock('WP_Post');
	$post->ID = $id;

	return $post;
}
