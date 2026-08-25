<!-- View Comments Modal Component - Facebook Style (Username from API) -->
<style>
    /* ---------- Modal Base ---------- */
    #viewCommentsModelCompModal .modal-content {
        background: #0f0f16;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        color: #e2e8f0;
        backdrop-filter: blur(20px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
    }
    #viewCommentsModelCompModal .modal-header {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 1.2rem 1.8rem;
        border-radius: 24px 24px 0 0;
        color: white;
    }
    #viewCommentsModelCompModal .modal-header .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.7;
    }
    #viewCommentsModelCompModal .modal-header .btn-close:hover {
        opacity: 1;
    }

    /* ---------- Comment Card ---------- */
    .comment-item-comp {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 18px;
        padding: 1rem 1.2rem;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 4px solid #7c5cfc;
        transition: all 0.2s;
        backdrop-filter: blur(10px);
    }
    .comment-item-comp:hover {
        background: rgba(255, 255, 255, 0.06);
        border-left-color: #a78bfa;
        box-shadow: 0 4px 20px rgba(124, 92, 252, 0.15);
    }
    .comment-item-comp .user-info strong {
        color: #f1f5f9;
    }
    .comment-item-comp .user-info small {
        color: #64748b;
    }
    .comment-item-comp .comment-text {
        color: #cbd5e1;
        margin-top: 0.4rem;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .reply-thread {
        margin-left: 2.5rem;
        border-left: 2px solid #4f46e5;
        padding-left: 1rem;
    }

    /* ---------- Buttons ---------- */
    #viewCommentsModelCompModal .btn-modern-sm {
        border: none;
        border-radius: 40px;
        padding: 0.35rem 1.1rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        backdrop-filter: blur(4px);
        cursor: pointer;
    }
    .btn-edit-modern {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.4);
    }
    .btn-edit-modern:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 18px rgba(37, 99, 235, 0.5);
        transform: translateY(-2px);
    }
    .btn-delete-modern {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.4);
    }
    .btn-delete-modern:hover {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
        box-shadow: 0 0 18px rgba(220, 38, 38, 0.5);
        transform: translateY(-2px);
    }
    .btn-reply-modern {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.4);
    }
    .btn-reply-modern:hover {
        background: #059669;
        color: #fff;
        border-color: #059669;
        box-shadow: 0 0 18px rgba(5, 150, 105, 0.5);
        transform: translateY(-2px);
    }
    .btn-cancel-modern {
        background: rgba(255,255,255,0.08);
        color: #94a3b8;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-cancel-modern:hover {
        background: rgba(255,255,255,0.2);
        color: #fff;
    }
    .btn-submit-modern {
        background: #10b981;
        color: #fff;
        border: none;
    }
    .btn-submit-modern:hover {
        background: #059669;
        box-shadow: 0 0 14px rgba(16,185,129,0.5);
        transform: translateY(-2px);
    }

    .reply-form-comp {
        background: rgba(255,255,255,0.03);
        border-radius: 14px;
        padding: 0.8rem;
        margin-top: 0.8rem;
        border: 1px solid rgba(255,255,255,0.06);
    }
    .reply-form-comp textarea {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        color: #f1f5f9;
        border-radius: 12px;
        padding: 0.6rem;
        width: 100%;
        resize: vertical;
        font-size: 0.9rem;
    }
    .reply-form-comp textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.3);
    }
</style>

<!-- Modal HTML -->
<div class="modal fade" id="viewCommentsModelCompModal" tabindex="-1" aria-labelledby="viewCommentsModelTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCommentsModelTitle">
                    <i class="fas fa-comments me-2"></i>Comments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewCommentsModelList">
                <p class="text-center text-muted">Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
    let viewCommentsModelCompModal;
    let __commentsMap = {};
    // Current user info will be set after fetching comments
    let currentUser = null;

    document.addEventListener('DOMContentLoaded', function () {
        viewCommentsModelCompModal = new bootstrap.Modal(document.getElementById('viewCommentsModelCompModal'));
    });

    function openViewCommentsModelComp(serviceId) {
        const modalBody = document.getElementById('viewCommentsModelList');
        modalBody.innerHTML = '<p class="text-center text-muted">Loading...</p>';
        viewCommentsModelCompModal.show();

        fetch(`includes/fetch_comments.php?service_id=${serviceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update current user from the response
                    if (data.current_user_username) {
                        currentUser = {
                            userId: data.current_user_id,
                            username: data.current_user_username
                        };
                    } else {
                        currentUser = null;
                    }

                    // Update modal header
                    const titleEl = document.getElementById('viewCommentsModelTitle');
                    if (currentUser) {
                        titleEl.innerHTML = '<i class="fas fa-comments me-2"></i>Comments - @' + escapeHtml(currentUser.username);
                    } else {
                        titleEl.innerHTML = '<i class="fas fa-comments me-2"></i>Comments - Preview Only';
                    }

                    if (data.comments.length) {
                        __commentsMap = {};
                        data.comments.forEach(c => { __commentsMap[c.comment_id] = c; });

                        const roots = buildCommentTree(data.comments);
                        let html = '';
                        roots.forEach(root => {
                            html += renderCommentTree(root, serviceId, 0);
                        });
                        modalBody.innerHTML = html;
                    } else {
                        modalBody.innerHTML = '<div class="text-center p-4"><i class="far fa-comment-dots fa-2x text-muted mb-2"></i><p>No comments yet.</p></div>';
                    }
                } else {
                    modalBody.innerHTML = '<div class="text-center text-danger p-4">Error loading comments.</div>';
                }
            })
            .catch(() => {
                modalBody.innerHTML = '<div class="text-center text-danger p-4">Error loading comments.</div>';
            });
    }

    function buildCommentTree(comments) {
        const map = {};
        const roots = [];
        comments.forEach(c => {
            map[c.comment_id] = { ...c, children: [] };
        });
        comments.forEach(c => {
            if (c.parent_comment_id && map[c.parent_comment_id]) {
                map[c.parent_comment_id].children.push(map[c.comment_id]);
            } else if (!c.parent_comment_id) {
                roots.push(map[c.comment_id]);
            }
        });
        return roots;
    }

    function renderCommentTree(comment, serviceId, depth) {
        let html = renderCommentItem(comment, serviceId, depth);
        if (comment.children && comment.children.length) {
            html += `<div class="reply-thread">`;
            comment.children.forEach(child => {
                html += renderCommentTree(child, serviceId, depth + 1);
            });
            html += `</div>`;
        }
        return html;
    }

    function renderCommentItem(c, serviceId, depth = 0) {
        const cleanText = escapeHtml(c.comment).replace(/'/g, "\\'");
        const editedTag = c.is_edited ? ' <span class="badge bg-secondary ms-1" style="font-size:0.7rem;">Edited</span>' : '';
        const marginLeft = depth > 0 ? '2.5rem' : '0';

        let replyTo = '';
        if (c.parent_comment_id && __commentsMap[c.parent_comment_id]) {
            const parent = __commentsMap[c.parent_comment_id];
            replyTo = `<div class="small text-primary mb-1">↳ replying to @${escapeHtml(parent.username)}</div>`;
        }

        let buttons = '';
        if (c.can_edit_delete) {
            buttons += `
                <button class="btn-modern-sm btn-edit-modern" onclick="editOwnCommentComp(${c.comment_id}, '${cleanText}', ${serviceId})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-modern-sm btn-delete-modern" onclick="deleteOwnCommentComp(${c.comment_id}, ${serviceId})">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>`;
        }
        if (c.can_reply) {
            buttons += `
                <button class="btn-modern-sm btn-reply-modern" onclick="toggleReplyFormComp(${c.comment_id}, ${serviceId}, '${escapeHtml(c.username)}')">
                    <i class="fas fa-reply"></i> Reply
                </button>`;
        }

        return `
        <div class="comment-item-comp" id="comment-${c.comment_id}" style="margin-left: ${marginLeft};">
            <div class="d-flex justify-content-between align-items-start user-info">
                <div>
                    <strong><i class="fas fa-user-circle me-1"></i>${escapeHtml(c.fullname)} (@${escapeHtml(c.username)})</strong>
                </div>
                <small><i class="far fa-clock me-1"></i>${new Date(c.created_at).toLocaleString()}${editedTag}</small>
            </div>
            ${replyTo}
            <div class="comment-text">${escapeHtml(c.comment)}</div>
            <div class="mt-2 d-flex gap-2">${buttons}</div>
            <div id="reply-form-${c.comment_id}" class="reply-form-comp mt-2" style="display: none;">
                <textarea id="reply-textarea-${c.comment_id}" rows="2" placeholder="Write a reply..."></textarea>
                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button class="btn-modern-sm btn-cancel-modern" onclick="toggleReplyFormComp(${c.comment_id}, ${serviceId}, '')">Cancel</button>
                    <button class="btn-modern-sm btn-submit-modern" onclick="submitReplyComp(${c.comment_id}, ${serviceId})"><i class="fas fa-paper-plane"></i> Reply</button>
                </div>
            </div>
        </div>`;
    }

    function toggleReplyFormComp(commentId, serviceId, username) {
        const formDiv = document.getElementById('reply-form-' + commentId);
        if (formDiv) {
            const isHidden = formDiv.style.display === 'none';
            formDiv.style.display = isHidden ? 'block' : 'none';
            if (isHidden) {
                const textarea = document.getElementById('reply-textarea-' + commentId);
                textarea.focus();
                if (username && textarea.value === '') {
                    textarea.value = '@' + username + ' ';
                }
            }
        }
    }

    function submitReplyComp(parentCommentId, serviceId) {
        const textarea = document.getElementById('reply-textarea-' + parentCommentId);
        const comment = textarea.value.trim();
        if (!comment) return;

        const fd = new FormData();
        fd.append('action', 'add');
        fd.append('service_id', serviceId);
        fd.append('comment', comment);
        fd.append('parent_comment_id', parentCommentId);

        fetch('includes/manage_comment.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openViewCommentsModelComp(serviceId);
            } else {
                alert('Reply failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('Network error. Reply failed.'));
    }

    function editOwnCommentComp(commentId, text, serviceId) {
        viewCommentsModelCompModal.hide();
        setTimeout(function () {
            if (typeof openCommentModelComp === 'function') {
                openCommentModelComp(serviceId, commentId, text);
            } else {
                alert('Comment modal not available.');
            }
        }, 300);
    }

    function deleteOwnCommentComp(commentId, serviceId) {
        if (!confirm('Are you sure you want to delete this comment? All replies will also be deleted.')) return;

        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('comment_id', commentId);
        fd.append('service_id', serviceId);

        fetch('includes/manage_comment.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openViewCommentsModelComp(serviceId);
            } else {
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('Network error. Delete failed.'));
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
</script>