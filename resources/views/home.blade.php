<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-name" content="{{ auth()->user()->name }}">
    @endauth
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtBlog - Home</title>
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
        </div>{{-- end sidebar --}}

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

                {{-- ====== POSTS FEED ====== --}}
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
                            <a href="{{ route('fullpost', $posts->id) }}" style="text-decoration:none; cursor:pointer;">
                                <img style="width:100%; max-width:500px; aspect-ratio:4/5; object-fit:cover;"
                                     src="{{ asset('img/' . $posts->image) }}"
                                     alt="{{ $posts->title }}">
                            </a>
                        </div>

                        <div class="post-footer">
                            <div class="post-reactions">
                                <div class="reaction-count">
                                    <span class="reaction-emoji" style="margin-right:5px;">
                                        {{ $posts->reactions->isNotEmpty()
                                            ? ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'][$posts->reactions->sortByDesc('created_at')->first()->type]
                                            : '' }}
                                    </span>
                                    <span class="reaction-num">{{ $posts->reactions->count() }}</span>
                                </div>
                                <div class="comment-count">{{ $posts->comments->count() }} comments</div>
                            </div>

                            <div class="post-buttons">

                                {{-- Reaction button --}}
                                <div class="reaction-btn-container"
                                     data-post-id="{{ $posts->id }}"
                                     data-reactors="{{ $posts->reactions->map(fn($r) => ['name' => $r->user->name, 'type' => $r->type])->toJson() }}">
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
                                {{-- END reaction-btn-container --}}

                                {{-- Comment button --}}
                                <div class="comment-btn-container">
                                    @auth
                                        <button class="comment-btn"
                                            data-post-id="{{ $posts->id }}"
                                            data-post-title="{{ $posts->title }}"
                                            data-post-image="{{ asset('img/' . $posts->image) }}"
                                            data-reaction-count="{{ $posts->reactions->count() }}"
                                            data-comment-count="{{ $posts->comments->count() }}"
                                            data-reaction-emojis="{{ $posts->reactions->groupBy('type')->keys()->map(fn($type) => ['like'=>'👍','love'=>'❤️','haha'=>'😂','wow'=>'😮','sad'=>'😢','angry'=>'😡'][$type])->join('') }}">
                                            <i class="far fa-comment-alt"></i> Comment
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" style="color:#1877f2;">Log in to comment</a>
                                    @endauth
                                </div>
                                {{-- END comment-btn-container --}}

                            </div>{{-- end post-buttons --}}
                        </div>{{-- end post-footer --}}

                    </div>{{-- end post-card --}}
                    @empty
                        <p style="text-align:center; color:#65676b; padding:20px;">No posts yet. Check back soon!</p>
                    @endforelse

                </div>{{-- end posts-container --}}
                {{-- ====== END POSTS FEED ====== --}}

            </section>
        </div>{{-- end center-content --}}

    </div>{{-- end main-content-wrapper --}}

    {{-- ====== COMMENT MODAL ====== --}}
    <div id="comment-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:white; border-radius:12px; width:100%; max-width:600px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden;">

            {{-- Modal Header --}}
            <div style="padding:16px; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center;">
                <h3 id="modal-post-title" style="margin:0; font-size:1.1em; font-weight:700;">Post</h3>
                <button id="close-modal" style="background:#f0f2f5; border:none; font-size:1.2em; cursor:pointer; width:36px; height:36px; border-radius:50%;">✕</button>
            </div>

            {{-- Scrollable content --}}
            <div style="flex:1; overflow-y:auto; display:flex; flex-direction:column;">

                {{-- Post Image --}}
                <div style="width:100%; background:#000; display:flex; justify-content:center;">
                    <img id="modal-image" src="" alt="" style="width:100%; max-height:350px; object-fit:contain;">
                </div>

                {{-- Reaction + Comment counts --}}
                <div style="padding:8px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd;">
                    <div style="display:flex; align-items:center; gap:4px;">
                        <span id="modal-reaction-emojis" style="font-size:1.1em;"></span>
                        <span id="modal-reaction-count" style="color:#65676b; font-size:0.9em;">0</span>
                    </div>
                    <div style="color:#65676b; font-size:0.9em;">
                        <span id="modal-comment-count">0 comments</span>
                    </div>
                </div>

                {{-- Comments List --}}
                <div id="modal-comments-list" style="padding:16px; display:flex; flex-direction:column; gap:12px;">
                    <p style="color:#65676b; text-align:center;">Loading comments...</p>
                </div>

            </div>{{-- end scrollable --}}

            {{-- Comment Input --}}
            @auth
            <div style="padding:12px 16px; border-top:1px solid #ddd; display:flex; gap:10px; align-items:center;">
                <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <input type="text" id="modal-comment-input" placeholder="Write a comment..."
                    style="flex:1; background:#f0f2f5; border:none; border-radius:20px; padding:10px 16px; outline:none;">
                <button id="modal-comment-submit" style="background:none; border:none; color:#1877f2; font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            @else
            <div style="padding:12px 16px; border-top:1px solid #ddd; text-align:center;">
                <a href="{{ route('login') }}" style="color:#1877f2;">Log in to comment</a>
            </div>
            @endauth

        </div>{{-- end modal inner --}}
    </div>{{-- end comment-modal --}}
    {{-- ====== END COMMENT MODAL ====== --}}

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

</div>{{-- end facebook-container --}}

<div class="overlay"></div>

</body>
</html>