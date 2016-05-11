<?php

namespace App\Http\Controllers;

use App\Post;
use Carbon\Carbon;

class CmsController extends Controller
{
    public function index()
    {
        $posts = Post::where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc')
            ->paginate(config('cms.posts_per_page'));

        return view('cms.index', compact('posts'));
    }

    public function showPost($slug)
    {
        $post = Post::whereSlug($slug)->firstOrFail();
        return view('cms.post')->withPost($post);
    }
}
