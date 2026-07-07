<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtBlog - Home</title>
    {{-- OLD: <link rel="stylesheet" href="{{asset('homestyle.css')}}"> --}}
    {{-- NEW: switched to @vite since files moved to resources/ --}}
    @vite(['resources/css/home.css', 'resources/js/home.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="facebook-container">

    <nav class="top-nav">
        <div class="top-left">
            <div class="logo">
                <a href="{{ route('home') }}" style="color:inherit; text-decoration:none;">Art<span>Blog</span></a>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search ArtBlog">
            </div>
        </div>
        <div class="top-right">
            @auth
                <a href="{{ route('dashboard') }}" class="profile-nav-link">
                    <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" style="font-weight:600;">Login</a>
            @endauth
            <i class="fas fa-bell"></i>
            <i class="fas fa-sort-down menu-btn"></i>
        </div>
    </nav>

    <div class="main-content-wrapper">

        <div class="sidebar off-canvas-right">
            <div class="sidebar-header">
                <h2>Menu</h2>
            </div>

            @auth
            <div class="sidebar-item">
                <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; margin-right:10px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span>{{ auth()->user()->name }}</span>
            </div>
            @endauth

            <div class="sidebar-item">
                <i class="fas fa-home"></i>
                <span><a href="{{ route('home') }}">Home</a></span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-palette"></i>
                <span>Gallery</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-info-circle"></i>
                <span>About</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-envelope"></i>
                <span>Contact</span>
            </div>
            @auth
            <div class="sidebar-item">
                <i class="fas fa-tachometer-alt"></i>
                <span><a href="{{ route('dashboard') }}">Dashboard</a></span>
            </div>

            {{-- OLD: <span>Log Out</span> plain text --}}
            {{-- NEW: proper Laravel POST logout form with @csrf --}}
            <div class="sidebar-item">
                <i class="fas fa-sign-out-alt"></i>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none; border:none; cursor:pointer; font-size:1em;">Log Out</button>
                </form>
            </div>
            @else
            <div class="sidebar-item">
                <i class="fas fa-sign-in-alt"></i>
                <span><a href="{{ route('login') }}">Login</a></span>
            </div>
            @endauth
        </div>

        <div class="center-content">
            <section id="home-section" class="content-section active">

                @auth
                <div class="create-post-container">
                    <div class="post-input-area">
                        <div style="width:40px; height:40px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <input type="text" 
                            placeholder="What's on your mind, {{ auth()->user()->name }}?"
                            onclick="window.location='{{ route('admin.addpost') }}'">
                    </div>
                    <div class="post-options">
                        <div class="option"><i class="fas fa-image photo-icon"></i> Photo/Video</div>
                        <div class="option"><i class="fas fa-palette feeling-icon"></i> Artwork</div>
                    </div>
                </div>
                @endauth

                {{-- ====== POSTS FEED - MAIN DYNAMIC SECTION ====== --}}
                <div class="posts-container">

                    @forelse($post as $posts)
                    <div class="post-card">

                        <div class="post-header">
                            <div style="width:40px; height:40px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; margin-right:10px;">
                                {{ strtoupper(substr($posts->user_name, 0, 1)) }}
                            </div>
                            <div class="post-user-info">
                                <p class="post-username">{{ $posts->user_name }}</p>
                                <p class="post-time">{{ $posts->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="post-body">
                            <p>{{ Str::limit($posts->description, 150) }}</p>
                            <a href="{{ route('fullpost', $posts->id) }}" style="text-decoration: none; cursor: pointer;">
                                <img style="width: 100%; max-width: 500px; aspect-ratio: 4 / 5; object-fit: cover; cursor: pointer;" src="{{ asset('img/' . $posts->image) }}" alt="{{ $posts->title }}">
                            </a>
                        </div>

                        <div class="post-footer">
                            <div class="post-reactions">
                                <div class="reaction-count">
                                    <span class="reaction-emoji" style="margin-right: 5px;"></span>
                                    <span class="reaction-num">0</span>
                                </div>
                                <div class="comment-count">{{ $posts->comments->count() }} comments</div>
                            </div>
                            <div class="post-buttons">
                                <div class="reaction-btn-container">
                                    <button class="reaction-btn" data-post-id="{{ $posts->id }}">
                                        <i class="far fa-heart"></i> Like
                                    </button>
                                    <div class="reaction-picker">
                                        <div class="reaction-option" data-reaction="like" title="Like"><i class="fas fa-heart"></i> Like</div>
                                        <div class="reaction-option" data-reaction="love" title="Love"><i class="fas fa-heart"></i> Love</div>
                                        <div class="reaction-option" data-reaction="haha" title="Haha"><i class="fas fa-grin-squint"></i> Haha</div>
                                        <div class="reaction-option" data-reaction="wow" title="Wow"><i class="fas fa-surprise"></i> Wow</div>
                                        <div class="reaction-option" data-reaction="sad" title="Sad"><i class="fas fa-frown"></i> Sad</div>
                                        <div class="reaction-option" data-reaction="angry" title="Angry"><i class="fas fa-angry"></i> Angry</div>
                                    </div>
                                </div>
                                <button class="comment-btn"><i class="far fa-comment-alt"></i> Comment</button>
                            </div>
                        </div>

                    </div>

                    
                    @empty
                        <p style="text-align:center; color:#65676b; padding:20px;">No posts yet. Check back soon!</p>
                    @endforelse

                </div>
                {{-- ====== END POSTS FEED ====== --}}

            </section>
        </div>

    </div>

   
    <nav class="bottom-nav">
        <a href="{{ route('home') }}" class="nav-icon active" data-section="home-section">
            <i class="fas fa-home"></i>
        </a>
        <a href="#" class="nav-icon"><i class="fas fa-palette"></i></a>
        <a href="#" class="nav-icon"><i class="fas fa-image"></i></a>
        @auth
        <a href="{{ route('dashboard') }}" class="nav-icon">
            <i class="fas fa-tachometer-alt"></i>
        </a>
        @endauth
    </nav>

</div>

<div class="overlay"></div>

</body>
</html>