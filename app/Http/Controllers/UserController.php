<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class UserController extends Controller
{
    public function  showDataInHome(){
        $post = Post::with(['comments', 'reactions.user'])->get();
        return view('home', compact('post'));
    }

    public function showFullPost($id){
        $post=Post::findOrFail($id);
        return view('fullpost', compact('post')); 
    }

    public function home(Request $request ){
        if ($request->user()->usertype == 'user'){
                return view('dashboard');               
            }
        else{
            return redirect()->route('admin.dashboard');
        }

    }

    public function index(Request $request){
        if ($request->user()->usertype == 'admin'){
                return view('admin.dashboard');               
            }
        else{
            return redirect()->route('dashboard');
        }
    }
}
