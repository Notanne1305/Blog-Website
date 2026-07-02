<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comment added!');
    }
}