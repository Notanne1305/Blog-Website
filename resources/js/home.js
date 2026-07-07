document.addEventListener('DOMContentLoaded', () => {
    const navIcons = document.querySelectorAll('.nav-icon');
    const sections = document.querySelectorAll('.content-section');
    const menuBtn = document.querySelector('.menu-btn');
    const sidebar = document.querySelector('.off-canvas-right');
    const overlay = document.querySelector('.overlay');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Navigation
    function handleNavigation(element) {
        const targetSectionId = element.dataset.section;
        navIcons.forEach(i => i.classList.remove('active'));
        if (element.classList.contains('nav-icon')) {
            element.classList.add('active');
        }
        sections.forEach(s => s.classList.remove('active'));
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

    // Mobile Menu
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Reaction emoji map
    const reactionData = {
        like:  { emoji: '👍', icon: 'fas fa-thumbs-up',   color: '#1877f2' },
        love:  { emoji: '❤️', icon: 'fas fa-heart',        color: '#e74c3c' },
        haha:  { emoji: '😂', icon: 'fas fa-grin-squint',  color: '#ffc107' },
        wow:   { emoji: '😮', icon: 'fas fa-surprise',     color: '#ffc107' },
        sad:   { emoji: '😢', icon: 'fas fa-frown',        color: '#ffc107' },
        angry: { emoji: '😠', icon: 'fas fa-angry',        color: '#ff6b6b' }
    };

    // Reaction button toggle picker
    document.querySelectorAll('.reaction-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const picker = btn.parentElement.querySelector('.reaction-picker');
            picker.style.opacity = picker.style.opacity === '1' ? '0' : '1';
            picker.style.visibility = picker.style.visibility === 'visible' ? 'hidden' : 'visible';
        });
    });

    // Reaction option click — saves to DB via fetch
    document.querySelectorAll('.reaction-option').forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();

            const reaction = option.dataset.reaction;
            const picker = option.closest('.reaction-picker');
            const btn = picker.previousElementSibling;
            const postId = btn.dataset.postId;
            const container = btn.closest('.post-card');
            const reactionCount = container.querySelector('.reaction-num');
            const reactionEmoji = container.querySelector('.reaction-emoji');
            const reactionBtnContainer = container.querySelector('.reaction-btn-container');

            // Close picker
            picker.style.opacity = '0';
            picker.style.visibility = 'hidden';

            // ---- SAVE TO DATABASE ----
            fetch(`/post/${postId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ type: reaction }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update count from DB response
                    reactionCount.textContent = data.total;

                    // Update emoji
                    reactionEmoji.textContent = reactionData[reaction].emoji;

                    // Update button appearance
                    btn.innerHTML = `<i class="${reactionData[reaction].icon}"></i> ${reaction.charAt(0).toUpperCase() + reaction.slice(1)}`;
                    btn.style.color = reactionData[reaction].color;
                    btn.classList.add('active');

                    // Update data-reactors for popup
                    reactionBtnContainer.dataset.reactors = JSON.stringify(data.users);
                }
            })
            .catch(err => {
                console.error('Reaction error:', err);
            });
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

    // Show who reacted on click of reaction count
    document.querySelectorAll('.reaction-count').forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', function () {
            const container = this.closest('.post-footer')
                                  .querySelector('.reaction-btn-container');
            const reactors = JSON.parse(container.dataset.reactors || '[]');

            if (reactors.length === 0) {
                alert('No reactions yet.');
                return;
            }

            // Build popup
            let html = `
                <div id="reactor-popup" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; border-radius:12px; padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.3); z-index:9999; min-width:280px; max-height:400px; overflow-y:auto;">
                    <h3 style="margin:0 0 12px;">Reactions</h3>
                    <span onclick="document.getElementById('reactor-popup').remove(); document.getElementById('reactor-overlay').remove();" style="position:absolute; top:12px; right:16px; cursor:pointer; font-size:1.2em;">✕</span>
            `;

            reactors.forEach(r => {
                html += `
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                        <span style="font-size:1.4em;">${reactionData[r.type]?.emoji ?? '👍'}</span>
                        <span>${r.name}</span>
                    </div>
                `;
            });

            html += `</div>
                <div id="reactor-overlay" onclick="this.remove(); document.getElementById('reactor-popup').remove();" style="position:fixed; inset:0; background:rgba(0,0,0,0.3); z-index:9998;"></div>
            `;

            document.body.insertAdjacentHTML('beforeend', html);
        });
    });

    // Load initial reaction state on page load
    document.querySelectorAll('.reaction-btn-container').forEach(container => {
        const postId = container.dataset.postId;
        const reactors = JSON.parse(container.dataset.reactors || '[]');
        const btn = container.querySelector('.reaction-btn');
        const postCard = container.closest('.post-card');
        if (!postCard || !btn) return;

        const reactionEmoji = postCard.querySelector('.reaction-emoji');
        const reactionCount = postCard.querySelector('.reaction-num');

        // Check if current logged-in user already reacted
        const loggedInUserName = document.querySelector('meta[name="user-name"]')?.content;
        if (loggedInUserName) {
            const userReaction = reactors.find(r => r.name === loggedInUserName);
            if (userReaction) {
                const data = reactionData[userReaction.type];
                btn.innerHTML = `<i class="${data.icon}"></i> ${userReaction.type.charAt(0).toUpperCase() + userReaction.type.slice(1)}`;
                btn.style.color = data.color;
                btn.classList.add('active');
            }
        }
    });
});