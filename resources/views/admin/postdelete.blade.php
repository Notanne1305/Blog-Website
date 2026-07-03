<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::check() && Auth::user()->usertype == 'admin')
                {{__('Admin Dashboard')}}
            @else
                {{__('User Dashboard')}}
            @endif
        </h2>
    </x-slot>
     @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" style="text-align:center; font-size:20px; border:1px solid blue;">

                    <h1>Post Id: </h1> {{$post->id}}
                    <!-- single post -->
                    <form style="text-allin:center;"action="{{route('admin.postdelete', $post->id)}}" method="post"> 
                    @csrf
                        <input type="text" name="id" placeholder="Enter Post Id here!" required> <br><br><br>
                        
                        <input style="border:1px solid blue; text-align:center; 
                        padding:10px;" type="submit" name="submit" 
                        value="Delete Post">

                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>
