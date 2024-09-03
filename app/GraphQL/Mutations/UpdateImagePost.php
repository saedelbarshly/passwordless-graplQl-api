<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Post;

final readonly class UpdateImagePost
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
        $file = $args['image'];
        $path = $file->storage_path('files');
        $post = Post::find($args['id']);
        $post->image = $path;
        $post->save();

    }
}
