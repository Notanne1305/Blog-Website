<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBlog - Home</title>
    <link rel="stylesheet" href="{{asset('homestyle.css')}}">
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to MyBlog</h1>
            <p>Discover amazing articles, tutorials, and insights about web development, Laravel, and modern PHP practices.</p>
            <a href="/blog" class="btn btn-primary">Browse Articles</a>
        </div>
    </section>

    <!-- Featured Posts -->
    <div class="container">
        <h2 class="section-title">All Posts</h2>
       
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

            <div class="comments-list">
                @forelse($post->comments as $comment)
                    <div style="display:flex; gap:10px; margin-bottom:16px;">
                        <div style="width:40px; height:40px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="background:#f0f2f5; border-radius:18px; padding:8px 12px; display:inline-block;">
                                <strong style="font-size:0.9em;">{{ $comment->user->name }}</strong>
                                <p style="margin:2px 0 0; font-size:0.95em;">{{ $comment->body }}</p>
                            </div>
                            <div style="font-size:0.8em; color:#65676b; margin-top:4px; padding-left:12px;">
                                <a href="#" style="font-weight:600; margin-right:10px;">Like</a>
                                <a href="#" style="font-weight:600; margin-right:10px;">Reply</a>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color:#65676b;">No comments yet. Be the first to comment!</p>
                @endforelse
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
</body>
</html>