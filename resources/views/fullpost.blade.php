<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-name" content="{{ auth()->user()->name }}">
    @endauth
    <title>MyBlog - Home</title>
    <link rel="stylesheet" href="{{asset('homestyle.css')}}">
    @vite(['resources/css/home.css', 'resources/js/home.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo">Lara<span>Blog</span></a>
                <div class="nav-links">
                    <a href="{{route('home')}}" class="active">Home</a>
                    <a href="">Blog</a>
                    <a href="">About</a>
                    <a href="">Contact</a>
                    @if (Route::has('login'))
                    @auth
                        <a
                            href="{{route('dashboard')}}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                    <a href="{{route('login')}}">Login</a>
                    @endauth

                    @endif
                </div>
            </nav>
        </div>
    </header>



    <!-- Featured Posts -->
    <div class="container">
        <div class="featured-posts">
            <!-- single post -->
            
            <div class="max-w-4xl mx-auto px-4 py-8">
                <!-- Post Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
                    <div class="flex items-center text-gray-500 text-sm">
                        <span>Published on {{ $post->created_at->format('F j, Y') }}</span>
                        <span class="mx-2">•</span>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($post->image)
                <div class="mb-8 rounded-lg overflow-hidden mx-auto block">
                    <img style="width: 600px;" src="{{ asset('img/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-auto object-cover">
                </div>
                @endif

                <!-- Post Content -->
                <div class="prose max-w-none mb-12">
                    {!! $post->description !!}
                </div>
            </div>
        </div> 
        </div>

       <!-- Comments Section -->
        <div class="container">
            <h2 class="section-title">Comments</h2>

            {{-- Dynamic comments loaded via JS --}}
            <div id="fullpost-comments-list" style="display:flex; flex-direction:column; gap:12px; margin-top:16px;">
                <p style="color:#65676b; text-align:center;">Loading comments...</p>
            </div>

            {{-- Comment input --}}
            @auth
            <div style="display:flex; gap:10px; align-items:center; margin-top:16px;">
                <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <input type="text" id="fullpost-comment-input" placeholder="Write a comment..."
                    style="flex:1; background:#f0f2f5; border:none; border-radius:20px; padding:10px 16px; outline:none;">
                <button id="fullpost-comment-submit" style="background:none; border:none; color:#1877f2; font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            @else
            <p style="margin-top:16px;"><a href="{{ route('login') }}" style="color:#1877f2;">Log in to comment</a></p>
            @endauth
        </div>

            <form action="{{ route('comment.store', $post) }}" method="POST" style="display:flex; gap:10px; align-items:center; margin-top:16px;">
                @csrf
                <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <input type="text" name="body" required placeholder="Write a comment..."
                    style="flex:1; background:#f0f2f5; border:none; border-radius:20px; padding:10px 16px; outline:none;">
                <button type="submit" style="background:none; border:none; color:#1877f2; font-weight:600; cursor:pointer;">Post</button>
            </form>
        </div>

        <!-- Categories -->
        <h2 class="section-title">Browse Categories</h2>
        <div class="categories">
            <a href="/category/laravel" class="category-tag">Laravel</a>
            <a href="/category/php" class="category-tag">PHP</a>
            <a href="/category/javascript" class="category-tag">JavaScript</a>
            <a href="/category/vue" class="category-tag">Vue.js</a>
            <a href="/category/tailwind" class="category-tag">Tailwind CSS</a>
            <a href="/category/testing" class="category-tag">Testing</a>
            <a href="/category/deployment" class="category-tag">Deployment</a>
            <a href="/category/performance" class="category-tag">Performance</a>
        </div>

        <!-- Newsletter -->
        <div class="newsletter">
            <h3>Subscribe to our Newsletter</h3>
            <p>Get the latest articles and news delivered to your inbox every week. No spam, ever.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>About LaraBlog</h3>
                    <p>A blog dedicated to Laravel, PHP, and modern web development practices. We share tutorials, tips, and industry insights.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/blog">Blog</a></li>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="/category/laravel">Laravel</a></li>
                        <li><a href="/category/php">PHP</a></li>
                        <li><a href="/category/javascript">JavaScript</a></li>
                        <li><a href="/category/vue">Vue.js</a></li>
                        <li><a href="/category/testing">Testing</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2026 Jonathan Sindo. All rights reserved. Built with Laravel.</p>
            </div>
        </div>
    </footer>

    <script>
        const fullpostId = {{ $post->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.addEventListener('DOMContentLoaded', () => {
            const list = document.getElementById('fullpost-comments-list');
            const input = document.getElementById('fullpost-comment-input');
            const submitBtn = document.getElementById('fullpost-comment-submit');

            // Load comments
            fetch(`/post/${fullpostId}/comments`)
                .then(res => res.json())
                .then(comments => {
                    if (comments.length === 0) {
                        list.innerHTML = '<p style="color:#65676b; text-align:center;">No comments yet. Be the first!</p>';
                        return;
                    }
                    list.innerHTML = comments.map(c => renderComment(c)).join('');
                    attachCommentEvents(list);
                });

            // Submit comment
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    const body = input.value.trim();
                    if (!body) return;

                    fetch(`/post/${fullpostId}/comment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ body }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const noComments = list.querySelector('p');
                            if (noComments) noComments.remove();

                            const newCommentEl = document.createElement('div');
                            newCommentEl.innerHTML = renderComment({
                                id: data.comment.id,
                                body: data.comment.body,
                                user_name: data.comment.user_name,
                                created_at: 'Just now',
                                reaction_count: 0,
                                reaction_emoji: '',
                                replies: [],
                            });
                            list.appendChild(newCommentEl.firstElementChild);
                            attachCommentEvents(list);
                            input.value = '';
                        }
                    });
                });

                // Submit on Enter
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') submitBtn.click();
                });
            }
        });
        </script>

</body>
</html>