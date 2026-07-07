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

    // Comment Modal
        const modal = document.getElementById('comment-modal');
        const closeModal = document.getElementById('close-modal');
        const commentsList = document.getElementById('modal-comments-list');
        const commentInput = document.getElementById('modal-comment-input');
        const commentSubmit = document.getElementById('modal-comment-submit');
        const modalImage = document.getElementById('modal-image');
        const modalPostTitle = document.getElementById('modal-post-title');
        const modalReactionEmojis = document.getElementById('modal-reaction-emojis');
        const modalReactionCount = document.getElementById('modal-reaction-count');
        const modalCommentCount = document.getElementById('modal-comment-count');
        let activePostId = null;

        // Open modal
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                activePostId = btn.dataset.postId;

                // populate modal header
                modalPostTitle.textContent = btn.dataset.postTitle || 'Post';

                // populate image
                modalImage.src = btn.dataset.postImage || '';
                modalImage.alt = btn.dataset.postTitle || '';

                // populate counts
                modalReactionCount.textContent = btn.dataset.reactionCount || '0';
                modalCommentCount.textContent = (btn.dataset.commentCount || '0') + ' comments';
                modalReactionEmojis.textContent = btn.dataset.reactionEmojis || '';

                // show modal
                modal.style.display = 'flex';

                // fetch comments
                fetch(`/post/${activePostId}/comments`)
                    .then(res => res.json())
                    .then(comments => {
                        if (comments.length === 0) {
                            commentsList.innerHTML = '<p style="color:#65676b; text-align:center;">No comments yet. Be the first!</p>';
                            return;
                        }
                        commentsList.innerHTML = comments.map(c => `
                            <div style="display:flex; gap:10px; margin-bottom:8px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                    ${c.user_name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div style="background:#f0f2f5; border-radius:18px; padding:8px 12px; display:inline-block;">
                                        <strong style="font-size:0.9em;">${c.user_name}</strong>
                                        <p style="margin:2px 0 0; font-size:0.95em;">${c.body}</p>
                                    </div>
                                    <div style="font-size:0.8em; color:#65676b; margin-top:4px; padding-left:12px;">
                                        <a href="#" style="font-weight:600; margin-right:10px; text-decoration:none; color:#65676b;">Like</a>
                                        <a href="#" style="font-weight:600; margin-right:10px; text-decoration:none; color:#65676b;">Reply</a>
                                        <span>${c.created_at}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    });
            });
        });

        // Close modal
        function closeCommentModal() {
            modal.style.display = 'none';
            activePostId = null;
            commentsList.innerHTML = '';
            if (commentInput) commentInput.value = '';
        }

        closeModal.addEventListener('click', closeCommentModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeCommentModal();
        });

        // Submit comment
        if (commentSubmit) {
            commentSubmit.addEventListener('click', () => {
                const body = commentInput.value.trim();
                if (!body || !activePostId) return;

                fetch(`/post/${activePostId}/comment`, {
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
                        // append new comment to list
                        const newComment = document.createElement('div');
                        newComment.style.cssText = 'display:flex; gap:10px; margin-bottom:8px;';
                        newComment.innerHTML = `
                            <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                ${data.comment.user_name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="background:#f0f2f5; border-radius:18px; padding:8px 12px; display:inline-block;">
                                    <strong style="font-size:0.9em;">${data.comment.user_name}</strong>
                                    <p style="margin:2px 0 0; font-size:0.95em;">${data.comment.body}</p>
                                </div>
                                <div style="font-size:0.8em; color:#65676b; margin-top:4px; padding-left:12px;">
                                    <a href="#" style="font-weight:600; margin-right:10px; text-decoration:none; color:#65676b;">Like</a>
                                    <a href="#" style="font-weight:600; margin-right:10px; text-decoration:none; color:#65676b;">Reply</a>
                                    <span>Just now</span>
                                </div>
                            </div>
                        `;

                        // remove "no comments" message if present
                        const noComments = commentsList.querySelector('p');
                        if (noComments) noComments.remove();

                        commentsList.appendChild(newComment);
                        commentInput.value = '';

                        // update comment count in modal
                        const currentCount = parseInt(modalCommentCount.textContent) || 0;
                        modalCommentCount.textContent = `${currentCount + 1} comments`;

                        // update comment count on post card
                        const postCard = document.querySelector(`.reaction-btn[data-post-id="${activePostId}"]`)?.closest('.post-card');
                        if (postCard) {
                            const countEl = postCard.querySelector('.comment-count');
                            if (countEl) {
                                const current = parseInt(countEl.textContent) || 0;
                                countEl.textContent = `${current + 1} comments`;
                            }
                            // also update the button's data attribute
                            const commentBtn = postCard.querySelector('.comment-btn');
                            if (commentBtn) {
                                commentBtn.dataset.commentCount = (parseInt(commentBtn.dataset.commentCount) || 0) + 1;
                            }
                        }
                    }
                });
            });

            // Submit on Enter
            commentInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') commentSubmit.click();
            });
        }
});