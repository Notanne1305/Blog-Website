<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class CommentController extends Controller
{
   public function store(Request $request, Post $post)
    {
    $request->validate(['body' => 'required|string|max:1000']);

    $comment = $post->comments()->create([
        'user_id' => Auth::id(),
        'body' => $request->body,
    ]);

    return response()->json([
        'success' => true,
        'comment' => [
            'id' => $comment->id,
            'body' => $comment->body,
            'user_name' => Auth::user()->name,
            'created_at' => $comment->created_at->diffForHumans(),
        ]
    ]);
    }

    public function fetch(Post $post)
    {
        $comments = $post->comments()
        ->with(['user', 'reactions', 'replies.user'])
        ->latest()
        ->get();

    return response()->json($comments->map(fn($c) => [
        'id' => $c->id,
        'body' => $c->body,
        'user_name' => $c->user->name,
        'created_at' => $c->created_at->diffForHumans(),
        'reaction_count' => $c->reactions->count(),
        'reaction_emoji' => $c->reactions->isNotEmpty()
            ? ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'][$c->reactions->sortByDesc('created_at')->first()->type]
            : '',
        'replies' => $c->replies->map(fn($r) => [
            'id' => $r->id,
            'body' => $r->body,
            'user_name' => $r->user->name,
            'created_at' => $r->created_at->diffForHumans(),
        ]),
    ]));
    }
}