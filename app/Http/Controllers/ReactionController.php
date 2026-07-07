<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'type' => 'required|in:like,love,haha,wow,sad,angry'
        ]);

        // if user already reacted, update it; otherwise create new
        Reaction::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['type' => $request->type]
        );

        return response()->json([
            'success' => true,
            'counts' => $post->reactions()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'total' => $post->reactions()->count(),
            'users' => $post->reactions()->with('user')
                ->get()
                ->map(fn($r) => [
                    'name' => $r->user->name,
                    'type' => $r->type,
                ]),
        ]);
    }

    public function destroy(Post $post)
    {
        Reaction::where('post_id', $post->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }
}