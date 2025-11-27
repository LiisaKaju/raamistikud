<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;


class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postFactory = Post ::factory(5)
        ->has(Comment::factory(3),'comments');

        Author::factory(10)
        ->has($postFactory,'posts')
        ->create();
    }
}
