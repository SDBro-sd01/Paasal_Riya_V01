<!-- Modern Comment Modal with Own Comments List -->
<style>
    /* ---------- Scoped styles for comment modal ---------- */
    #commentModelCompModal .modal-content {
        background: #0f0f16;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        color: #e2e8f0;
        backdrop-filter: blur(20px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
    }
    #commentModelCompModal .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 1.2rem 1.8rem;
        background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
        border-radius: 24px 24px 0 0;
    }
    #commentModelCompModal .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
        padding: 1rem 1.8rem;
    }
    #commentModelCompModal .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.7;
        transition: 0.2s;
    }
    #commentModelCompModal .btn-close:hover {
        opacity: 1;
    }
    #commentModelCompModal .form-control {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 16px !important;
        padding: 0.75rem 1rem;
        transition: 0.2s;
    }
    #commentModelCompModal .form-control:focus {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important;
    }
    #commentModelCompModal .form-control::placeholder {
        color: #64748b !important;
    }

    /* Modern comment card (for my comments list) */
    #commentModelCompModal .comment-item-comp {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 16px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.8rem;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 4px solid #7c5cfc;
        transition: all 0.2s;
    }
    #commentModelCompModal .comment-item-comp:hover {
        background: rgba(255, 255, 255, 0.06);
        border-left-color: #a78bfa;
        box-shadow: 0 4px 20px rgba(124, 92, 252, 0.15);
    }
    #commentModelCompModal .comment-item-comp .comment-meta {
        font-size: 0.8rem;
        color: #64748b;
    }
    #commentModelCompModal .comment-item-comp .comment-text {
        color: #cbd5e1;
        font-size: 0.9rem;
        margin-top: 0.3rem;
    }

    /* ---------- SCROLLABLE MY COMMENTS ---------- */
    #commentModelMyComments {
        max-height: 250px;        /* අවශ්‍ය නම් වෙනස් කරන්න */
        overflow-y: auto;
        padding-right: 6px;       /* scrollbar සහ text අතර ඉඩ */
        margin-top: 0.5rem;
    }

    /* ---------- MODERN BUTTON STYLES (local) ---------- */
    #commentModelCompModal .btn-modern {
        border: none;
        border-radius: 40px;
        padding: 0.6rem 1.6rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        backdrop-filter: blur(4px);
    }
    #commentModelCompModal .btn-modern-sm {
        padding: 0.3rem 0.9rem;
        font-size: 0.75rem;
        gap: 4px;
    }
    #commentModelCompModal .btn-edit-modern {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.4);
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.15);
    }
    #commentModelCompModal .btn-edit-modern:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 18px rgba(37, 99, 235, 0.5);
        transform: translateY(-2px);
    }
    #commentModelCompModal .btn-delete-modern {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.15);
    }
    #commentModelCompModal .btn-delete-modern:hover {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
        box-shadow: 0 0 18px rgba(220, 38, 38, 0.5);
        transform: translateY(-2px);
    }
    #commentModelCompModal .btn-post-modern {
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }
    #commentModelCompModal .btn-post-modern:hover {
        background: linear-gradient(135deg, #047857, #059669);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
        transform: translateY(-2px);
    }
    #commentModelCompModal .btn-cancel-modern {
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    #commentModelCompModal .btn-cancel-modern:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
    }
</style>

<!-- Modal HTML -->
<div class="modal fade" id="commentModelCompModal" tabindex="-1" aria-labelledby="commentModelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="commentModelTitle">
                    <i class="fas fa-comment-dots me-2"></i>Write Comment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="commentModelServiceId">
                <input type="hidden" id="commentModelEditId">
                <textarea id="commentModelText" rows="4" class="form-control" placeholder="Share your experience..."></textarea>
                <div id="commentModelMessage" class="text-danger mt-2 small"></div>

                <!-- My Comments Section -->
                <h6 class="mt-3 mb-2 text-uppercase" style="font-size:0.75rem; letter-spacing:1px;">
                    <i class="fas fa-history me-1"></i> Your Comments
                </h6>
                <!-- Scrollable container -->
                <div id="commentModelMyComments">
                    <p class="text-center  small">Loading your comments...</p>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn-modern btn-delete-modern" id="commentModelDeleteBtn" style="display: none;">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn-modern btn-cancel-modern" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-modern btn-post-modern" id="commentModelSubmitBtn">
                        <i class="fas fa-paper-plane"></i> Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let commentModelCompModal;
    let currentServiceId = null; // to reuse in fetch

    document.addEventListener('DOMContentLoaded', function () {
        commentModelCompModal = new bootstrap.Modal(document.getElementById('commentModelCompModal'));
    });

    /**
     * Open the comment modal for adding or editing a comment,
     * and load the current user's own comments for the service.
     */
    function openCommentModelComp(serviceId, editId = null, editText = '') {
        currentServiceId = serviceId;
        document.getElementById('commentModelServiceId').value = serviceId;
        document.getElementById('commentModelEditId').value = editId || '';
        document.getElementById('commentModelText').value = editText || '';
        document.getElementById('commentModelMessage').innerHTML = '';

        const titleEl = document.getElementById('commentModelTitle');
        const submitBtn = document.getElementById('commentModelSubmitBtn');
        const deleteBtn = document.getElementById('commentModelDeleteBtn');

        if (editId) {
            titleEl.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Comment';
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Save';
            deleteBtn.style.display = 'inline-flex';
        } else {
            titleEl.innerHTML = '<i class="fas fa-comment-dots me-2"></i>Write Comment';
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Post';
            deleteBtn.style.display = 'none';
        }

        // Load current user's comments for this service
        loadMyComments(serviceId);
        commentModelCompModal.show();
    }

    /**
     * Fetch and display only the comments belonging to the current user.
     */
    function loadMyComments(serviceId) {
        const container = document.getElementById('commentModelMyComments');
        container.innerHTML = '<p class="text-center small">Loading your comments...</p>';

        fetch(`includes/fetch_comments.php?service_id=${serviceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.comments.length) {
                    // Filter only comments that the current user can edit/delete (i.e., own comments)
                    const myComments = data.comments.filter(c => c.can_edit_delete);
                    if (myComments.length === 0) {
                        container.innerHTML = '<p class="text-center text-muted small">You haven’t commented yet.</p>';
                        return;
                    }
                    let html = '';
                    myComments.forEach(c => {
                        const cleanText = escapeHtml(c.comment).replace(/'/g, "\\'");
                        html += `
                        <div class="comment-item-comp">
                            <div class="comment-meta">
                                <i class="far fa-clock me-1"></i>${new Date(c.created_at).toLocaleString()}
                            </div>
                            <div class="comment-text">${escapeHtml(c.comment)}</div>
                            <div class="mt-2 d-flex gap-2">
                                <button class="btn-modern btn-modern-sm btn-edit-modern"
                                    onclick="startEditOwnComment(${c.comment_id}, '${cleanText}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-modern btn-modern-sm btn-delete-modern"
                                    onclick="deleteMyComment(${c.comment_id})">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>`;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-center small">No comments yet.</p>';
                }
            })
            .catch(() => {
                container.innerHTML = '<p class="text-center text-danger small">Failed to load comments.</p>';
            });
    }

    /**
     * Switch the modal to edit mode for a given comment (no page reload).
     */
    function startEditOwnComment(commentId, text) {
        document.getElementById('commentModelEditId').value = commentId;
        document.getElementById('commentModelText').value = text;
        document.getElementById('commentModelMessage').innerHTML = '';

        document.getElementById('commentModelTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Comment';
        document.getElementById('commentModelSubmitBtn').innerHTML = '<i class="fas fa-save me-1"></i> Save';
        document.getElementById('commentModelDeleteBtn').style.display = 'inline-flex';
    }

    /**
     * Delete a comment from the my-comments list.
     */
    function deleteMyComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('comment_id', commentId);
        formData.append('service_id', currentServiceId);

        fetch('includes/manage_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Reload my comments list
                loadMyComments(currentServiceId);
                // If we were editing the deleted comment, reset form
                if (document.getElementById('commentModelEditId').value == commentId) {
                    document.getElementById('commentModelEditId').value = '';
                    document.getElementById('commentModelText').value = '';
                    document.getElementById('commentModelTitle').innerHTML = '<i class="fas fa-comment-dots me-2"></i>Write Comment';
                    document.getElementById('commentModelSubmitBtn').innerHTML = '<i class="fas fa-paper-plane me-1"></i> Post';
                    document.getElementById('commentModelDeleteBtn').style.display = 'none';
                }
            } else {
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('Network error.'));
    }

    // Submit (Add/Edit) - unchanged logic, but also refresh my comments after success
    document.getElementById('commentModelSubmitBtn').addEventListener('click', function () {
        const serviceId = document.getElementById('commentModelServiceId').value;
        const text = document.getElementById('commentModelText').value.trim();
        const editId = document.getElementById('commentModelEditId').value;

        if (!text) {
            document.getElementById('commentModelMessage').innerHTML = 'Comment cannot be empty.';
            return;
        }

        const formData = new FormData();
        formData.append('action', editId ? 'edit' : 'add');
        formData.append('service_id', serviceId);
        formData.append('comment', text);
        if (editId) formData.append('comment_id', editId);

        fetch('includes/manage_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Refresh my comments list and reset to "new comment" state
                loadMyComments(serviceId);
                document.getElementById('commentModelEditId').value = '';
                document.getElementById('commentModelText').value = '';
                document.getElementById('commentModelTitle').innerHTML = '<i class="fas fa-comment-dots me-2"></i>Write Comment';
                document.getElementById('commentModelSubmitBtn').innerHTML = '<i class="fas fa-paper-plane me-1"></i> Post';
                document.getElementById('commentModelDeleteBtn').style.display = 'none';
                document.getElementById('commentModelMessage').innerHTML = '';
                // If you want to update parent grid, call loadServices
                if (typeof loadServices === 'function') loadServices();
            } else {
                document.getElementById('commentModelMessage').innerHTML = data.message || 'Error submitting comment.';
            }
        })
        .catch(() => {
            document.getElementById('commentModelMessage').innerHTML = 'Network error. Please try again.';
        });
    });

    // Delete from edit mode (the delete button in footer)
    document.getElementById('commentModelDeleteBtn').addEventListener('click', function () {
        const commentId = document.getElementById('commentModelEditId').value;
        if (!commentId) return;
        deleteMyComment(commentId);
    });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
</script>