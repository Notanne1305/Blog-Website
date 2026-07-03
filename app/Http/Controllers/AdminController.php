<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function addpost()
    {
        return view('admin.add_post');
    }

    public function createpost(Request $request){
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $image = $request->file('image');
        $imagename = time() . '.' . $image->getClientOriginalExtension();
    
        $post->image = $imagename;
        $post->user_name = Auth::user()->name;
        $post->user_id = Auth::user()->id;

        if($post->save()){
            $image->move(public_path('img'), $imagename);
            return redirect()->route('admin.allpost')->with('status', 'Added Successfully!');
        }       
    }

    public function allpost(){
        $post = Post::all();
        return view('admin.allpost', compact('post'));

    }

    public function updatePost($id){
        $post=Post::findOrFail($id);
        return view('admin.updatepost', compact('post'));
    }
    
    public function postupdate(Request $request, $id ){
        $post= Post::findOrFail($id);
        $post->title = $request->title;
        $post->description = $request->description;
      
        if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagename = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('img'), $imagename);
        $post->image = $imagename;
        }
       
        $post->user_name = Auth::user()->name;
        $post->user_id = Auth::user()->id;
        $post->save();
  
        return redirect()->route('admin.allpost')->with('status', 'Updated Successfully!');    
    }  
    
    public function deletePost($id){
        $post = Post::findOrFail($id);
        return view('admin.postdelete', compact('post'));
        // return redirect()->route('admin.allpost')->with('status', 'Deleted Successfully!');
    }

    public function postDelete(Request $request, $id){
        $post = Post::findOrFail($id);
        if ($request->id==$post->id){
         $post->delete();
        return redirect()->route('admin.allpost')->with('danger', 'Deleted Successfully!');
        }
        else{
            return redirect()->route('admin.allpost')->with('warning', 'Invalid Post ID!');
        }
    }
}
   

       

