document.addEventListener('DOMContentLoaded', () => {
    const navIcons = document.querySelectorAll('.nav-icon');
    const sections = document.querySelectorAll('.content-section');
    const likeButtons = document.querySelectorAll('.like-button');
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.off-canvas-right');
    const overlay = document.querySelector('.overlay');

    // Handle both top and bottom navigation
    function handleNavigation(element) {
        const targetSectionId = element.dataset.section;

        // Remove active class from all nav icons
        navIcons.forEach(i => i.classList.remove('active'));
        
        // Add active class to the clicked icon (if it has a section)
        if (element.classList.contains('nav-icon')) {
            element.classList.add('active');
        }

        // Hide all sections
        sections.forEach(s => s.classList.remove('active'));
        
        // Show the target section
        if (targetSectionId) {
            document.getElementById(targetSectionId).classList.add('active');
        }
    }

    navIcons.forEach(icon => {
        icon.addEventListener('click', (e) => {
            e.preventDefault();
            handleNavigation(icon);
        });
    });

    // Mobile Menu Logic
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Like Button Logic
    likeButtons.forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('liked');
            const icon = button.querySelector('i');
            
            if (button.classList.contains('liked')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
    });

    // Reaction System Logic
    const reactionBtns = document.querySelectorAll('.reaction-btn');
    const reactionOptions = document.querySelectorAll('.reaction-option');

    const reactionData = {
        like: { emoji: '👍', icon: 'fas fa-heart', color: '#1877f2' },
        love: { emoji: '❤️', icon: 'fas fa-heart', color: '#e74c3c' },
        haha: { emoji: '😂', icon: 'fas fa-grin-squint', color: '#ffc107' },
        wow: { emoji: '😮', icon: 'fas fa-surprise', color: '#ffc107' },
        sad: { emoji: '😢', icon: 'fas fa-frown', color: '#ffc107' },
        angry: { emoji: '😠', icon: 'fas fa-angry', color: '#ff6b6b' }
    };

    // Track reactions per post (client-side storage for demo)
    const postReactions = {};

    reactionBtns.forEach(btn => {
        const postId = btn.dataset.postId;
        if (!postReactions[postId]) {
            postReactions[postId] = { reaction: null, count: 0 };
        }

        btn.addEventListener('click', () => {
            const container = btn.parentElement;
            const picker = container.querySelector('.reaction-picker');
            
            // Toggle picker visibility on click
            picker.style.opacity = picker.style.opacity === '1' ? '0' : '1';
            picker.style.visibility = picker.style.visibility === 'visible' ? 'hidden' : 'visible';
        });
    });

    reactionOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();
            
            const reaction = option.dataset.reaction;
            const picker = option.closest('.reaction-picker');
            const btn = picker.previousElementSibling;
            const postId = btn.dataset.postId;
            const container = btn.closest('.post-card');
            const reactionCount = container.querySelector('.reaction-num');
            const reactionEmoji = container.querySelector('.reaction-emoji');

            // Update reaction data
            if (postReactions[postId].reaction === reaction) {
                // Toggle off if same reaction clicked
                postReactions[postId].reaction = null;
                postReactions[postId].count = 0;
                btn.classList.remove('active');
                reactionEmoji.textContent = '';
                reactionCount.textContent = '0';
                btn.innerHTML = '<i class="far fa-heart"></i> Like';
            } else {
                // Add or change reaction
                postReactions[postId].reaction = reaction;
                postReactions[postId].count = 1;
                btn.classList.add('active');
                
                const data = reactionData[reaction];
                reactionEmoji.textContent = data.emoji;
                reactionCount.textContent = '1';
                
                btn.innerHTML = `<i class="${data.icon}"></i> ${reaction.charAt(0).toUpperCase() + reaction.slice(1)}`;
                btn.style.color = data.color;
            }

            // Close picker
            picker.style.opacity = '0';
            picker.style.visibility = 'hidden';
        });
    });

    // Close picker when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.reaction-btn-container')) {
            document.querySelectorAll('.reaction-picker').forEach(picker => {
                picker.style.opacity = '0';
                picker.style.visibility = 'hidden';
            });
        }
    });
});
