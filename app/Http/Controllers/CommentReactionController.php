<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\CommentReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentReactionController extends Controller
{
    // React to a comment
    public function react(Request $request, Comment $comment)
    {
        $request->validate([
            'type' => 'required|in:like,love,haha,wow,sad,angry'
        ]);

        CommentReaction::updateOrCreate(
            ['comment_id' => $comment->id, 'user_id' => Auth::id()],
            ['type' => $request->type]
        );

        return response()->json([
            'success' => true,
            'total' => $comment->reactions()->count(),
            'emoji' => ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'][$request->type],
        ]);
    }

    // Reply to a comment
    public function reply(Request $request, Comment $comment)
    {
        $request->validate([
            'body' => 'required|string|max:1000'
        ]);

        $reply = $comment->replies()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return response()->json([
            'success' => true,
            'reply' => [
                'id' => $reply->id,
                'body' => $reply->body,
                'user_name' => Auth::user()->name,
                'created_at' => $reply->created_at->diffForHumans(),
            ]
        ]);
    }
}