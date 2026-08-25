<?php
include "includes/db_connection.php";

$Page_Name = "Favorite";

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;

if (!$isLogged || $user_id == 0) {
    header("Location: log_in.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>My Favorites - Paasal Riya</title>
    <style>
        /* ── Global enchanted base ── */
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #070b15 !important;
            color: #e8eaf2;
            min-height: 100vh;
            overflow-x: hidden;
        }


        /* Main container sits above stars */
        .container {
            position: relative;
            z-index: 1;
            max-width: 1300px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Glass page header */
        .page-header {
            background: rgba(15, 15, 30, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(124, 92, 252, 0.25);
            border-radius: 24px;
            padding: 1.2rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            transition: 0.3s;
        }
        .page-header:hover {
            border-color: rgba(167, 139, 250, 0.5);
            box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 20px rgba(124,92,252,0.15);
        }
        .page-header h2 {
            color: #f0f1ff;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header h2 i {
            text-shadow: 0 0 10px rgba(244,63,94,0.7);
        }
        .page-header .btn-light {
            background: rgba(124,92,252,0.15);
            border: 1px solid rgba(124,92,252,0.3);
            color: #c4b5fd;
            border-radius: 50px;
            font-size: .85rem;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: 0.3s;
            backdrop-filter: blur(4px);
        }
        .page-header .btn-light:hover {
            background: rgba(124,92,252,0.25);
            border-color: #a78bfa;
            color: #fff;
            box-shadow: 0 0 15px rgba(124,92,252,0.4);
        }

        /* ── Grid & Cards (enchanted) ── */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 1.8rem;
        }
        .post-card {
            background: rgba(15, 15, 30, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(124, 92, 252, 0.2);
            border-radius: 24px;
            padding: 1.2rem;
            position: relative;
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1);
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .post-card::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(124,92,252,0.1) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
            z-index: 0;
        }
        .post-card:hover::before { opacity: 1; }
        .post-card:hover {
            transform: translateY(-8px);
            border-color: rgba(167,139,250,0.5);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(124,92,252,0.3);
        }
        .post-card::after {
            content: '✨';
            position: absolute;
            top: -15px; right: -15px;
            font-size: 2rem;
            opacity: 0;
            transition: 0.5s;
            z-index: 1;
            pointer-events: none;
            filter: drop-shadow(0 0 8px #fbbf24);
        }
        .post-card:hover::after {
            opacity: 1;
            top: -5px; right: -5px;
        }

        .card-actions { position: absolute; top: 1rem; right: 1rem; z-index: 3; }
        .remove-fav-btn {
            background: rgba(15,15,30,0.8);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(244,63,94,0.3);
            border-radius: 50%;
            width: 38px; height: 38px;
            color: #f43f5e;
            cursor: pointer;
            transition: 0.3s;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
        }
        .remove-fav-btn:hover {
            background: #f43f5e;
            color: #fff;
            box-shadow: 0 0 15px rgba(244,63,94,0.6);
            transform: scale(1.1);
        }

        .service-image {
            width: 100%; height: 200px;
            object-fit: cover;
            border-radius: 18px;
            cursor: pointer;
            margin-bottom: 1rem;
            background: rgba(10,12,24,0.8);
            border: 1px solid rgba(124,92,252,0.2);
            transition: 0.3s;
        }
        .post-card:hover .service-image {
            border-color: rgba(167,139,250,0.5);
            box-shadow: 0 0 25px rgba(124,92,252,0.2);
        }

        .badge.bg-dark {
            background: rgba(0,0,0,0.7) !important;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 50px;
            font-size: 0.7rem;
            backdrop-filter: blur(8px);
            letter-spacing: 0.5px;
        }

        h5.mt-2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #f0f1ff;
            margin-top: 0.3rem !important;
            position: relative;
            z-index: 1;
        }

        .rating-display {
            display: flex; align-items: center; gap: 8px;
            margin: 0.5rem 0; font-size: 0.8rem; color: #fbbf24;
            position: relative; z-index: 1;
        }
        .rating-display span:last-child { color: #9ca3af; }

        .info {
            font-size: 0.8rem; color: #9ca3af; margin: 0.25rem 0;
            display: flex; align-items: center; gap: 8px;
            position: relative; z-index: 1;
        }
        .info i { width: 18px; color: #a78bfa; text-align: center; font-size: 0.9rem; }

        .button-group {
            display: flex; gap: 8px; margin-top: 1rem; flex-wrap: wrap;
            position: relative; z-index: 1;
        }

        /* Gradient enchanted buttons */
        .see-more-btn, .feedback-btn, .comment-btn, .view-comments-btn, .report-btn {
            flex: 1 1 0;
            min-width: 70px;
            border: none;
            border-radius: 40px;
            padding: 10px 6px;
            font-weight: 700;
            font-size: 0.78rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            backdrop-filter: blur(4px);
            position: relative;
            overflow: hidden;
        }

        .see-more-btn {
            background: linear-gradient(135deg, #7c5cfc, #a78bfa);
            color: #fff;
            box-shadow: 0 4px 15px rgba(124,92,252,0.4);
        }
        .see-more-btn:hover {
            box-shadow: 0 6px 25px rgba(124,92,252,0.7);
            transform: translateY(-2px);
            background: linear-gradient(135deg, #8b6cfc, #c4b5fd);
        }

        .feedback-btn {
            background: rgba(251,191,36,0.1);
            color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.4);
        }
        .feedback-btn:hover {
            background: rgba(251,191,36,0.2);
            box-shadow: 0 0 20px rgba(251,191,36,0.4);
            transform: translateY(-2px);
        }

        .comment-btn {
            background: rgba(52,211,153,0.1);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.4);
        }
        .comment-btn:hover {
            background: rgba(52,211,153,0.2);
            box-shadow: 0 0 20px rgba(52,211,153,0.4);
            transform: translateY(-2px);
        }

        .view-comments-btn {
            background: rgba(96,165,250,0.1);
            color: #60a5fa;
            border: 1px solid rgba(96,165,250,0.4);
        }
        .view-comments-btn:hover {
            background: rgba(96,165,250,0.2);
            box-shadow: 0 0 20px rgba(96,165,250,0.4);
            transform: translateY(-2px);
        }

        .report-btn {
            background: rgba(248,113,113,0.1);
            color: #f87171;
            border: 1px solid rgba(248,113,113,0.4);
        }
        .report-btn:hover {
            background: rgba(248,113,113,0.2);
            box-shadow: 0 0 20px rgba(248,113,113,0.4);
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center; padding: 4rem;
            background: rgba(15, 15, 30, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(124,92,252,0.25);
            border-radius: 24px;
            color: #7e8aa2;
            font-size: 1.2rem;
        }
        .empty-state .btn-light {
            background: rgba(124,92,252,0.15);
            border: 1px solid rgba(124,92,252,0.3);
            color: #c4b5fd;
            border-radius: 50px;
        }

        /* ── General modal overrides (dark theme) ── */
        .modal-content {
            background: rgba(10,12,24,0.95) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(124,92,252,0.3);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
        }
        .modal-header, .modal-footer { border-color: rgba(124,92,252,0.2) !important; }
        .modal-header .btn-close { filter: invert(1) opacity(0.6); }
        .form-control {
            background: rgba(10,12,24,0.8) !important;
            border: 1px solid rgba(124,92,252,0.3) !important;
            color: #e2e8f0 !important;
            border-radius: 12px !important;
        }
        .form-control:focus {
            border-color: #a78bfa !important;
            box-shadow: 0 0 15px rgba(124,92,252,0.4) !important;
        }

        .btn-primary { background: linear-gradient(135deg, #7c5cfc, #a78bfa) !important; border: none !important; }
        .btn-secondary { background: rgba(30,30,40,0.8) !important; border: 1px solid rgba(124,92,252,0.2) !important; color: #c4b5fd !important; }
        .btn-success { background: linear-gradient(135deg, #059669, #10b981) !important; border: none !important; }
        .btn-warning { background: linear-gradient(135deg, #d97706, #f59e0b) !important; border: none !important; }
        .btn-danger { background: linear-gradient(135deg, #dc2626, #f87171) !important; border: none !important; }

        .bg-success { background: #059669 !important; }
        .bg-info { background: #0284c7 !important; }
        .bg-danger { background: #dc2626 !important; }
        .bg-dark { background: #0f0f18 !important; }
        .text-white { color: #e2e8f0 !important; }

        /* ── Custom scrollbar ── */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: rgba(10,12,24,0.8); }
        ::-webkit-scrollbar-thumb { background: rgba(124,92,252,0.4); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(124,92,252,0.7); }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .post-card::after { display: none; }
        }
    </style>
</head>
<body>


<?php include "side_bar.php"; ?>
<?php include "stars_bg.php"; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-heart"></i> My Favorite Services</h2>
        <a href="index.php" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
    <div class="posts-grid" id="favoritesContainer"><div class="empty-state">Loading favorites...</div></div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5><i class="fas fa-flag me-2"></i>Report Service</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold mb-3">Select reason(s):</p>
                <div id="reportOptionsContainer"></div>
                <div class="mt-3" id="customReasonWrapper" style="display:none;">
                    <label for="customReason" class="form-label">Describe the issue</label>
                    <textarea class="form-control" id="customReason" rows="3" placeholder="Please provide details..."></textarea>
                </div>
                <div id="reportMessage" class="text-danger mt-2"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="submitReportBtn"><i class="fas fa-paper-plane me-2"></i>Submit Report</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== COMPONENT INCLUDES ========== -->
<?php include "components/see_more_modal.php"; ?>
<?php include "components/comment_model.php"; ?>
<?php include "components/view_comments_model.php"; ?>
<?php include "components/rating_model.php"; ?>
<?php include "components/image_viewing_model.php"; ?>



<script>

    // ── Favorites logic (unchanged) ──
    let reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
    let currentReportServiceId = null;

    function escapeHtml(str) { if (!str) return ''; return String(str).replace(/[&<>]/g, m => m==='&'?'&amp;': m==='<'?'&lt;':'&gt;'); }
    function formatCount(count) {
        if (count === null || count === undefined) return '0';
        if (count < 1000) return count.toString();
        if (count < 1000000) return (count / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
        if (count < 1000000000) return (count / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
        return (count / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
    }

    function loadFavorites() {
        fetch('includes/fetch_favorites.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('favoritesContainer');
            if (data.success && data.services.length) {
                let html = '';
                data.services.forEach(service => {
                    let avgRating = service.avg_rating || 0;
                    let totalRatings = service.total_ratings || 0;
                    let commentsCount = service.comments_count || 0;
                    let formattedComments = formatCount(commentsCount);
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= Math.round(avgRating)) starsHtml += '<i class="fas fa-star"></i>';
                        else if (i - 0.5 <= avgRating) starsHtml += '<i class="fas fa-star-half-alt"></i>';
                        else starsHtml += '<i class="far fa-star"></i>';
                    }
                    let schoolsPreview = service.schools.slice(0,2).join(', ')+(service.schools.length>2?'...':'');
                    let areasPreview = service.areas_covered ? service.areas_covered.split(',').slice(0,2).join(', ') : '';
                    if(service.areas_covered && service.areas_covered.split(',').length>2) areasPreview += '...';
                    let firstImage = (service.images && service.images.length) ? service.images[0].image_path : 'assets/default-car.jpg';
                    let imageCount = service.images ? service.images.length : 0;
                    html += `
                    <div class="post-card" data-service-id="${service.service_id}">
                        <div class="card-actions">
                            <button class="remove-fav-btn" onclick="removeFavorite(${service.service_id}, this)" title="Remove from favorites"><i class="fas fa-trash-alt"></i></button>
                        </div>
                        <img src="${firstImage}" class="service-image" onclick="openImageGallery(${service.service_id})" style="cursor:pointer;" onerror="this.src='assets/default-car.jpg';">
                        ${imageCount > 1 ? `<span class="badge bg-dark position-absolute top-0 start-0 m-2">${imageCount} photos</span>` : ''}
                        <h5 class="mt-2">${escapeHtml(service.service_name)}</h5>
                        <div class="rating-display"><span>${starsHtml}</span><span>(${avgRating}/5 · ${totalRatings} ratings)</span></div>
                        <div class="info"><i class="fas fa-id-card"></i> ${escapeHtml(service.reg_no)}</div>
                        <div class="info"><i class="fas fa-car"></i> ${escapeHtml(service.vehicle_type)}</div>
                        <div class="info"><i class="fas fa-tag"></i> ${escapeHtml(service.service_type)}</div>
                        <div class="info"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(service.province)}, ${escapeHtml(service.district)}</div>
                        <div class="info"><i class="fas fa-home"></i> ${escapeHtml(service.home_town)}</div>
                        <div class="info"><i class="fas fa-globe"></i> ${escapeHtml(areasPreview)}</div>
                        <div class="info"><i class="fas fa-school"></i> ${escapeHtml(schoolsPreview)}</div>
                        <div class="button-group">
                            <button class="see-more-btn" onclick="viewDetails(${service.service_id})"><i class="fas fa-eye"></i> See More</button>
                            <button class="view-comments-btn" onclick="openViewCommentsModelComp(${service.service_id})"><i class="fas fa-comments"></i> ${formattedComments} Comments</button>
                            <button class="report-btn" onclick="openReportModal(${service.service_id})"><i class="fas fa-flag"></i> Report</button>
                        </div>
                        <div class="button-group mt-2">
                            <button class="feedback-btn" onclick="openRatingModelComp(${service.service_id}, '${(service.service_name||'').replace(/'/g, "\\'")}', ${service.user_rating || 0})"><i class="fas fa-star"></i> Rate</button>
                            <button class="comment-btn" onclick="openCommentModelComp(${service.service_id})"><i class="fas fa-comment"></i> Give Comment</button>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state"><i class="far fa-heart"></i><h4>No favorites yet</h4><p>Go to Dashboard and heart services you like.</p><a href="index.php" class="btn btn-light mt-3">Browse Services</a></div>';
            }
        }).catch(() => container.innerHTML = '<div class="empty-state">Error loading favorites. Please refresh.</div>');
    }

    function removeFavorite(serviceId, btn) {
        const formData = new FormData();
        formData.append('service_id', serviceId);
        fetch('includes/toggle_favorite.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.action === 'removed') {
                const card = btn.closest('.post-card');
                if (card) card.remove();
                if (document.querySelectorAll('.post-card').length === 0) {
                    document.getElementById('favoritesContainer').innerHTML = '<div class="empty-state"><i class="far fa-heart"></i><h4>No favorites yet</h4><p>Add some from Dashboard.</p></div>';
                }
            } else alert('Failed to remove');
        }).catch(() => alert('Network error'));
    }

    function openReportModal(serviceId) {
        currentReportServiceId = serviceId;
        document.getElementById('customReason').value = '';
        document.getElementById('customReasonWrapper').style.display = 'none';
        document.getElementById('reportMessage').innerHTML = '';

        fetch('includes/get_report_options.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let html = '';
                    data.options.forEach(opt => {
                        html += `
                        <div class="form-check">
                            <input class="form-check-input report-option" type="checkbox" value="${opt.id}" id="opt${opt.id}" data-text="${escapeHtml(opt.option_text)}">
                            <label class="form-check-label" for="opt${opt.id}">${escapeHtml(opt.option_text)}</label>
                        </div>`;
                    });
                    document.getElementById('reportOptionsContainer').innerHTML = html;

                    document.querySelectorAll('.report-option').forEach(cb => {
                        cb.addEventListener('change', function() {
                            let otherChecked = false;
                            document.querySelectorAll('.report-option').forEach(c => {
                                if (c.checked && c.getAttribute('data-text').toLowerCase() === 'other') {
                                    otherChecked = true;
                                }
                            });
                            document.getElementById('customReasonWrapper').style.display = otherChecked ? 'block' : 'none';
                            if (!otherChecked) document.getElementById('customReason').value = '';
                        });
                    });

                    reportModal.show();
                } else {
                    alert('Failed to load report options');
                }
            });
    }

    document.getElementById('submitReportBtn').addEventListener('click', function() {
        let selected = [];
        document.querySelectorAll('.report-option:checked').forEach(cb => { selected.push(cb.value); });
        if (selected.length === 0) {
            document.getElementById('reportMessage').innerHTML = 'Please select at least one reason.';
            return;
        }
        if (!confirm('Are you 100% sure? This action cannot be undone.')) return;

        let customReason = document.getElementById('customReason').value;
        let fd = new FormData();
        fd.append('service_id', currentReportServiceId);
        fd.append('selected_options', JSON.stringify(selected));
        fd.append('custom_reason', customReason);

        fetch('includes/submit_report.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                reportModal.hide();
                alert('Report submitted. Thank you.');
            } else {
                document.getElementById('reportMessage').innerHTML = data.message || 'Submission failed.';
            }
        });
    });

    // Image viewer functions are handled by included component.
    function loadServices() {
        if (typeof loadFavorites === 'function') loadFavorites();
    }

    // Start
    loadFavorites();
</script>
</body>
</html>