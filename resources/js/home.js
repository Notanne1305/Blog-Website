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
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Reaction emoji map
    const reactionData = {
        like:  { emoji: '👍', icon: 'fas fa-thumbs-up',  color: '#1877f2' },
        love:  { emoji: '❤️', icon: 'fas fa-heart',       color: '#e74c3c' },
        haha:  { emoji: '😂', icon: 'fas fa-grin-squint', color: '#ffc107' },
        wow:   { emoji: '😮', icon: 'fas fa-surprise',    color: '#ffc107' },
        sad:   { emoji: '😢', icon: 'fas fa-frown',       color: '#ffc107' },
        angry: { emoji: '😠', icon: 'fas fa-angry',       color: '#ff6b6b' }
    };

    // reaction emojis map (used in renderComment)
    const reactionEmojis = {
        like: '👍', love: '❤️', haha: '😂',
        wow: '😮', sad: '😢', angry: '😡'
    };

    // ============================================================
    // renderComment — builds HTML string for a single comment
    // ============================================================
    function renderComment(c) {
        const repliesHtml = (c.replies || []).map(r => `
            <div style="display:flex; gap:8px; margin-top:8px; padding-left:44px;">
                <div style="width:28px; height:28px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8em; flex-shrink:0;">
                    ${r.user_name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div style="background:#f0f2f5; border-radius:18px; padding:6px 10px; display:inline-block;">
                        <strong style="font-size:0.85em;">${r.user_name}</strong>
                        <p style="margin:2px 0 0; font-size:0.9em;">${r.body}</p>
                    </div>
                    <div style="font-size:0.75em; color:#65676b; margin-top:2px; padding-left:10px;">
                        <span>${r.created_at}</span>
                    </div>
                </div>
            </div>
        `).join('');

        return `
            <div class="comment-item" data-comment-id="${c.id}" style="margin-bottom:12px;">
                <div style="display:flex; gap:10px;">
                    <div style="width:36px; height:36px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                        ${c.user_name.charAt(0).toUpperCase()}
                    </div>
                    <div style="flex:1;">
                        <div style="background:#f0f2f5; border-radius:18px; padding:8px 12px; display:inline-block; position:relative;">
                            <strong style="font-size:0.9em;">${c.user_name}</strong>
                            <p style="margin:2px 0 0; font-size:0.95em;">${c.body}</p>
                            ${c.reaction_count > 0 ? `
                            <span class="reaction-bubble" style="position:absolute; bottom:-10px; right:8px; background:white; border-radius:10px; padding:2px 6px; font-size:0.8em; box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                ${c.reaction_emoji} ${c.reaction_count}
                            </span>` : ''}
                        </div>
                        <div style="font-size:0.8em; color:#65676b; margin-top:8px; padding-left:12px; display:flex; gap:12px; align-items:center;">
                            <span style="color:#65676b; font-size:0.85em;">${c.created_at}</span>
                            <div class="comment-like-btn" data-comment-id="${c.id}" style="cursor:pointer; font-weight:600; position:relative;">
                                Like
                                <div class="comment-reaction-picker" style="display:none; position:absolute; bottom:24px; left:0; background:white; border-radius:24px; padding:6px 10px; box-shadow:0 2px 12px rgba(0,0,0,0.2); gap:6px; z-index:100; white-space:nowrap;">
                                    ${Object.entries(reactionEmojis).map(([type, emoji]) => `
                                        <span class="comment-reaction-option" data-reaction="${type}" data-comment-id="${c.id}" style="cursor:pointer; font-size:1.4em;" title="${type}">${emoji}</span>
                                    `).join('')}
                                </div>
                            </div>
                            <span class="comment-reply-toggle" data-comment-id="${c.id}" style="cursor:pointer; font-weight:600;">Reply</span>
                        </div>

                        <!-- Reply input hidden by default -->
                        <div class="reply-input-area" data-comment-id="${c.id}" style="display:none; margin-top:8px; padding-left:12px; gap:8px; align-items:center;">
                            <input type="text" class="reply-input" placeholder="Write a reply..."
                                style="flex:1; background:#f0f2f5; border:none; border-radius:20px; padding:8px 14px; outline:none; font-size:0.9em;">
                            <button class="reply-submit" data-comment-id="${c.id}" style="background:none; border:none; color:#1877f2; font-weight:600; cursor:pointer;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>

                        ${repliesHtml}
                    </div>
                </div>
            </div>
        `;
    }

    // ============================================================
    // attachCommentEvents — attaches like/reply events to comments
    // ============================================================
    function attachCommentEvents(container) {
        // Like button hover to show picker
        container.querySelectorAll('.comment-like-btn').forEach(btn => {
            const picker = btn.querySelector('.comment-reaction-picker');

            btn.addEventListener('mouseenter', () => {
                picker.style.display = 'flex';
            });
            btn.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    if (!picker.matches(':hover')) picker.style.display = 'none';
                }, 300);
            });
            picker.addEventListener('mouseleave', () => {
                picker.style.display = 'none';
            });
        });

        // Reaction option click
        container.querySelectorAll('.comment-reaction-option').forEach(option => {
            option.addEventListener('click', () => {
                const commentId = option.dataset.commentId;
                const reaction = option.dataset.reaction;

                fetch(`/comment/${commentId}/react`, {
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
                        const commentItem = container.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                        let bubble = commentItem.querySelector('.reaction-bubble');
                        if (!bubble) {
                            bubble = document.createElement('span');
                            bubble.className = 'reaction-bubble';
                            bubble.style.cssText = 'position:absolute; bottom:-10px; right:8px; background:white; border-radius:10px; padding:2px 6px; font-size:0.8em; box-shadow:0 1px 3px rgba(0,0,0,0.2);';
                            commentItem.querySelector('[style*="position:relative"]').appendChild(bubble);
                        }
                        bubble.textContent = `${data.emoji} ${data.total}`;
                        option.closest('.comment-reaction-picker').style.display = 'none';
                    }
                });
            });
        });

        // Reply toggle
        container.querySelectorAll('.comment-reply-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const commentId = btn.dataset.commentId;
                const replyArea = container.querySelector(`.reply-input-area[data-comment-id="${commentId}"]`);
                const isVisible = replyArea.style.display === 'flex';
                replyArea.style.display = isVisible ? 'none' : 'flex';
                if (!isVisible) replyArea.querySelector('.reply-input').focus();
            });
        });

        // Reply submit
        container.querySelectorAll('.reply-submit').forEach(btn => {
            btn.addEventListener('click', () => {
                const commentId = btn.dataset.commentId;
                const replyArea = container.querySelector(`.reply-input-area[data-comment-id="${commentId}"]`);
                const input = replyArea.querySelector('.reply-input');
                const body = input.value.trim();
                if (!body) return;

                fetch(`/comment/${commentId}/reply`, {
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
                        const newReply = document.createElement('div');
                        newReply.style.cssText = 'display:flex; gap:8px; margin-top:8px; padding-left:44px;';
                        newReply.innerHTML = `
                            <div style="width:28px; height:28px; border-radius:50%; background:#ccc; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8em; flex-shrink:0;">
                                ${data.reply.user_name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="background:#f0f2f5; border-radius:18px; padding:6px 10px; display:inline-block;">
                                    <strong style="font-size:0.85em;">${data.reply.user_name}</strong>
                                    <p style="margin:2px 0 0; font-size:0.9em;">${data.reply.body}</p>
                                </div>
                                <div style="font-size:0.75em; color:#65676b; margin-top:2px; padding-left:10px;">
                                    <span>Just now</span>
                                </div>
                            </div>
                        `;
                        replyArea.parentElement.appendChild(newReply);
                        input.value = '';
                        replyArea.style.display = 'none';
                    }
                });
            });

            // Submit reply on Enter
            if (btn.previousElementSibling) {
                btn.previousElementSibling.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') btn.click();
                });
            }
        });
    }

    // ============================================================
    // Post reaction button toggle picker
    // ============================================================
    document.querySelectorAll('.reaction-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const picker = btn.parentElement.querySelector('.reaction-picker');
            picker.style.opacity = picker.style.opacity === '1' ? '0' : '1';
            picker.style.visibility = picker.style.visibility === 'visible' ? 'hidden' : 'visible';
        });
    });

    // Post reaction option click — saves to DB
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

            picker.style.opacity = '0';
            picker.style.visibility = 'hidden';

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
                    reactionCount.textContent = data.total;
                    reactionEmoji.textContent = reactionData[reaction].emoji;
                    btn.innerHTML = `<i class="${reactionData[reaction].icon}"></i> ${reaction.charAt(0).toUpperCase() + reaction.slice(1)}`;
                    btn.style.color = reactionData[reaction].color;
                    btn.classList.add('active');
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

    // Show who reacted popup
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
        const reactors = JSON.parse(container.dataset.reactors || '[]');
        const btn = container.querySelector('.reaction-btn');
        const postCard = container.closest('.post-card');
        if (!postCard || !btn) return;

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

    // ============================================================
    // Comment Modal
    // ============================================================
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

    if (modal) {
        // Open modal
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                activePostId = btn.dataset.postId;
                modalPostTitle.textContent = btn.dataset.postTitle || 'Post';
                modalImage.src = btn.dataset.postImage || '';
                modalImage.alt = btn.dataset.postTitle || '';
                modalReactionCount.textContent = btn.dataset.reactionCount || '0';
                modalCommentCount.textContent = (btn.dataset.commentCount || '0') + ' comments';
                modalReactionEmojis.textContent = btn.dataset.reactionEmojis || '';
                modal.style.display = 'flex';

                // fetch comments
                fetch(`/post/${activePostId}/comments`)
                    .then(res => res.json())
                    .then(comments => {
                        if (comments.length === 0) {
                            commentsList.innerHTML = '<p style="color:#65676b; text-align:center;">No comments yet. Be the first!</p>';
                            return;
                        }
                        commentsList.innerHTML = comments.map(c => renderComment(c)).join('');
                        attachCommentEvents(commentsList);
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
                        const noComments = commentsList.querySelector('p');
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
                        commentsList.appendChild(newCommentEl.firstElementChild);
                        attachCommentEvents(commentsList);
                        commentInput.value = '';

                        // update counts
                        const currentCount = parseInt(modalCommentCount.textContent) || 0;
                        modalCommentCount.textContent = `${currentCount + 1} comments`;

                        // update post card comment count
                        const postCard = document.querySelector(`.reaction-btn[data-post-id="${activePostId}"]`)?.closest('.post-card');
                        if (postCard) {
                            const countEl = postCard.querySelector('.comment-count');
                            if (countEl) {
                                const current = parseInt(countEl.textContent) || 0;
                                countEl.textContent = `${current + 1} comments`;
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
    }
});