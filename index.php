<?php
include "includes/db_connection.php";
include "side_bar.php";   // <--- side_bar.php loads language variables & __t() function

// ───────── Merge index‑specific translations ─────────
// $lang and $translations are already defined in side_bar.php
$index_translation_file = __DIR__ . '/Languages_Files/index.json';
if (file_exists($index_translation_file)) {
    $json = file_get_contents($index_translation_file);
    $all_lang = json_decode($json, true);
    if (isset($all_lang[$lang])) {
        $translations = array_merge($translations ?? [], $all_lang[$lang]);
    } elseif (isset($all_lang['English'])) {
        $translations = array_merge($translations ?? [], $all_lang['English']);
    }
}
// ─────────────────────────────────────────────────

$Page_Name = "Dashboard";

// Fetch distinct service types for filter dropdown
$service_types = [];
$type_query = "SELECT DISTINCT service_type FROM services WHERE service_type IS NOT NULL AND service_type != '' ORDER BY service_type";
$type_result = mysqli_query($conn, $type_query);
if ($type_result) {
    while ($row = mysqli_fetch_assoc($type_result)) {
        $service_types[] = $row['service_type'];
    }
}

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title><?php echo __t('page_title', 'Paasal Riya - Home'); ?></title>
    <style>
        /* ── Global reset & enchanted foundation ── */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            min-height: 100vh;
            background: #070b15;
            color: #e8eaf2;
            overflow-x: hidden;
        }

        /* ── Main container with glass base ── */
        .dashboard-container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* ── Filters section (glass + glow) ── */
        .filters-section {
            background: rgba(15, 15, 25, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(124, 92, 252, 0.25);
            border-radius: 24px;
            padding: 1.8rem;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.3rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 80px rgba(124,92,252,0.08);
            transition: 0.3s;
        }
        .filters-section:hover {
            border-color: rgba(124, 92, 252, 0.5);
            box-shadow: 0 12px 50px rgba(0,0,0,0.5), 0 0 100px rgba(124,92,252,0.15);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 160px;
        }

        .filter-group label {
            color: #b3b8d9;
            font-weight: 600;
            display: block;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .filter-group select,
        .filter-group input {
            background: rgba(10, 12, 24, 0.8);
            border: 1px solid rgba(124, 92, 252, 0.3);
            border-radius: 50px;
            padding: 0.6rem 1.2rem;
            width: 100%;
            color: #e2e8f0;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s;
            backdrop-filter: blur(4px);
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.5), 0 0 0 3px rgba(124,92,252,0.15);
        }

        .filter-group select option {
            background: #0f0f20;
            color: #e2e8f0;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
        }

        .search-wrapper input {
            width: 100%;
            padding: .6rem 3.2rem .6rem 1.4rem;
            background: rgba(10,12,24,0.8);
            border: 1px solid rgba(124,92,252,0.35);
            border-radius: 50px;
            color: #e2e8f0;
            font-size: .9rem;
            outline: none;
            transition: 0.3s;
            backdrop-filter: blur(4px);
        }

        .search-wrapper input::placeholder {
            color: #6b7280;
            font-style: italic;
        }

        .search-wrapper input:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 20px rgba(124,92,252,0.4);
        }

        .search-wrapper button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #7c5cfc, #a78bfa);
            border: none;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            padding: .45rem 1.2rem;
            height: calc(100% - 10px);
            border-radius: 0 50px 50px 0;
            transition: 0.3s;
            font-weight: 600;
        }

        .search-wrapper button:hover {
            background: linear-gradient(135deg, #8b6cfc, #c4b5fd);
            box-shadow: 0 0 20px rgba(124,92,252,0.6);
            transform: translateY(-50%) scale(1.02);
        }

        .reset-btn {
            background: rgba(15,15,30,0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(124,92,252,0.3);
            border-radius: 50px;
            color: #c4b5fd;
            padding: .55rem 1.8rem;
            white-space: nowrap;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 500;
            transition: 0.3s;
        }

        .reset-btn:hover {
            border-color: #a78bfa;
            background: rgba(124,92,252,0.1);
            box-shadow: 0 0 15px rgba(124,92,252,0.3);
            color: #fff;
        }

        /* ── Posts Grid with enchanting cards ── */
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
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(124,92,252,0.1) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
            z-index: 0;
        }

        .post-card:hover::before {
            opacity: 1;
        }

        .post-card:hover {
            transform: translateY(-8px);
            border-color: rgba(167, 139, 250, 0.5);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(124,92,252,0.3);
        }

        /* Sparkle effect on hover */
        .post-card::after {
            content: '✨';
            position: absolute;
            top: -15px;
            right: -15px;
            font-size: 2rem;
            opacity: 0;
            transition: 0.5s;
            z-index: 1;
            pointer-events: none;
            filter: drop-shadow(0 0 8px #fbbf24);
        }
        .post-card:hover::after {
            opacity: 1;
            top: -5px;
            right: -5px;
        }

        .card-header-fav {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 3;
        }

        .favorite-btn {
            background: rgba(15,15,30,0.8);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(124,92,252,0.3);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            font-size: 1rem;
            color: #6b7280;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .favorite-btn.favorited {
            color: #f43f5e;
            border-color: rgba(244,63,94,0.6);
            box-shadow: 0 0 15px rgba(244,63,94,0.4);
        }
        .favorite-btn:hover {
            color: #f43f5e;
            border-color: rgba(244,63,94,0.8);
            transform: scale(1.1);
        }

        .service-image {
            width: 100%;
            height: 200px;
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
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0.5rem 0;
            font-size: 0.8rem;
            color: #fbbf24;
            position: relative;
            z-index: 1;
        }
        .rating-display span:last-child {
            color: #9ca3af;
        }

        .info {
            font-size: 0.8rem;
            color: #9ca3af;
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        .info i {
            width: 18px;
            color: #a78bfa;
            text-align: center;
            font-size: 0.9rem;
        }

        .button-group {
            display: flex;
            gap: 8px;
            margin-top: 1rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        /* Enchanted gradient buttons */
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
            background: rgba(251, 191, 36, 0.1);
            color: #fbbf24;
            border: 1px solid rgba(251,191,36,0.4);
        }
        .feedback-btn:hover {
            background: rgba(251,191,36,0.2);
            box-shadow: 0 0 20px rgba(251,191,36,0.4);
            transform: translateY(-2px);
        }

        .comment-btn {
            background: rgba(52, 211, 153, 0.1);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.4);
        }
        .comment-btn:hover {
            background: rgba(52,211,153,0.2);
            box-shadow: 0 0 20px rgba(52,211,153,0.4);
            transform: translateY(-2px);
        }

        .view-comments-btn {
            background: rgba(96, 165, 250, 0.1);
            color: #60a5fa;
            border: 1px solid rgba(96,165,250,0.4);
        }
        .view-comments-btn:hover {
            background: rgba(96,165,250,0.2);
            box-shadow: 0 0 20px rgba(96,165,250,0.4);
            transform: translateY(-2px);
        }

        .report-btn {
            background: rgba(248, 113, 113, 0.1);
            color: #f87171;
            border: 1px solid rgba(248,113,113,0.4);
        }
        .report-btn:hover {
            background: rgba(248,113,113,0.2);
            box-shadow: 0 0 20px rgba(248,113,113,0.4);
            transform: translateY(-2px);
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 4rem;
            background: rgba(15, 15, 30, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(124,92,252,0.25);
            border-radius: 24px;
            color: #7e8aa2;
            font-size: 1.2rem;
        }

        /* ── Modals ── */
        .modal-content {
            background: rgba(10, 12, 24, 0.95) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(124,92,252,0.3);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
        }
        .modal-header, .modal-footer {
            border-color: rgba(124,92,252,0.2) !important;
        }
        .modal-header .btn-close {
            filter: invert(1) opacity(0.6);
        }

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

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .filter-row { flex-direction: column; }
            .post-card::after { display: none; }
        }

        /* ── Custom scrollbar ── */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(10,12,24,0.8);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(124,92,252,0.4);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(124,92,252,0.7);
        }
    </style>
</head>
<body>

<?php include "stars_bg.php"; ?>

<?php // side_bar.php already included at top — do not include again ?>
<div class="dashboard-container">
    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-row">
            <div class="filter-group">
                <label><?php echo __t('filter_province', 'Province'); ?></label>
                <select id="filter-province">
                    <option value="">All</option>
                    <option>Western</option><option>Central</option><option>Southern</option>
                    <option>Northern</option><option>Eastern</option><option>North Western</option>
                    <option>North Central</option><option>Uva</option><option>Sabaragamuwa</option>
                </select>
            </div>
            <div class="filter-group">
                <label><?php echo __t('filter_district', 'District'); ?></label>
                <select id="filter-district">
                    <option value="">All</option>
                </select>
            </div>
            <div class="filter-group">
                <label><?php echo __t('filter_service_type', 'Service Type'); ?></label>
                <select id="filter-service-type">
                    <option value="">All</option>
                    <?php foreach ($service_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="filter-row">
            <div class="filter-group" style="flex: 2; min-width: 250px;">
                <label><?php echo __t('filter_service_type', 'Search'); ?></label>
                <div class="search-wrapper">
                    <input type="text" id="search-input" placeholder="<?php echo __t('search_placeholder', 'Service / School / Institute / Area'); ?>">
                    <button id="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div>
                <button id="reset-filters" class="reset-btn"><i class="fas fa-undo-alt"></i> <?php echo __t('reset', 'Reset'); ?></button>
            </div>
        </div>
    </div>

    <div class="posts-grid" id="postsContainer">
        <div class="empty-state"><?php echo __t('loading', 'Loading...'); ?></div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5><i class="fas fa-flag me-2"></i><?php echo __t('report_title', 'Report Service'); ?></h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="fw-bold mb-3"><?php echo __t('select_reasons', 'Select reason(s):'); ?></p>
        <div id="reportOptionsContainer"></div>
        <div class="mt-3" id="customReasonWrapper" style="display:none;">
          <label for="customReason" class="form-label"><?php echo __t('describe_issue', 'Describe the issue'); ?></label>
          <textarea class="form-control" id="customReason" rows="3" placeholder="<?php echo __t('custom_reason_placeholder', 'Please provide details...'); ?>"></textarea>
        </div>
        <div id="reportMessage" class="text-danger mt-2"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __t('cancel', 'Cancel'); ?></button>
        <button class="btn btn-danger" id="submitReportBtn"><i class="fas fa-paper-plane me-2"></i><?php echo __t('submit_report', 'Submit Report'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php include "components/image_viewing_model.php"; ?>
<?php include "components/see_more_modal.php"; ?>
<?php include "components/comment_model.php"; ?>
<?php include "components/view_comments_model.php"; ?>
<?php include "components/rating_model.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
 
    // Pass translations to JavaScript
    window.__indexTrans = <?php echo json_encode($translations); ?>;
</script>

<script>
const t = window.__indexTrans || {};
let currentUserLogged = <?php echo $isLogged ? 'true' : 'false'; ?>;
let reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
let currentReportServiceId = null;

// Province-District Mapping
const districtsByProvince = {
    "Western": ["Colombo", "Gampaha", "Kalutara"],
    "Central": ["Kandy", "Matale", "Nuwara Eliya"],
    "Southern": ["Galle", "Matara", "Hambantota"],
    "Northern": ["Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu"],
    "Eastern": ["Batticaloa", "Ampara", "Trincomalee"],
    "North Western": ["Kurunegala", "Puttalam"],
    "North Central": ["Anuradhapura", "Polonnaruwa"],
    "Uva": ["Badulla", "Monaragala"],
    "Sabaragamuwa": ["Ratnapura", "Kegalle"]
};
const allDistricts = [].concat(...Object.values(districtsByProvince));
const districtSelect = document.getElementById('filter-district');

function populateDistricts(selectedProvince) {
    districtSelect.innerHTML = '<option value="">All</option>';
    let districts = selectedProvince ? (districtsByProvince[selectedProvince] || allDistricts) : allDistricts;
    districts.forEach(district => {
        const option = document.createElement('option');
        option.value = district;
        option.textContent = district;
        districtSelect.appendChild(option);
    });
    districtSelect.value = '';
}

document.getElementById('filter-province').addEventListener('change', function() {
    populateDistricts(this.value);
    loadServices();
});
populateDistricts('');

function escapeHtml(str) { if(!str) return ''; return String(str).replace(/[&<>]/g, m => m==='&'?'&amp;': m==='<'?'&lt;':'&gt;'); }

function formatCount(count) {
    if (count === null || count === undefined) return '0';
    if (count < 1000) return count.toString();
    if (count < 1000000) return (count / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
    if (count < 1000000000) return (count / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
    return (count / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
}

function loadServices() {
    let province = document.getElementById('filter-province').value;
    let district = document.getElementById('filter-district').value;
    let serviceType = document.getElementById('filter-service-type').value;
    let search = document.getElementById('search-input').value;
    fetch(`includes/fetch_services.php?search=${encodeURIComponent(search)}&province=${encodeURIComponent(province)}&district=${encodeURIComponent(district)}&service_type=${encodeURIComponent(serviceType)}`)
    .then(res => res.json())
    .then(data => {
        let container = document.getElementById('postsContainer');
        if(data.success && data.services.length){
            let html = '';
            data.services.forEach(service => {
                let isFav = service.is_favorited;
                let favClass = isFav ? 'favorited' : '';
                let favIcon = isFav ? 'fas fa-heart' : 'far fa-heart';
                let avg = service.avg_rating || 0;
                let starsHtml = '';
                for(let i=1;i<=5;i++) starsHtml += i<=Math.round(avg)?'<i class="fas fa-star"></i>': (i-0.5<=avg?'<i class="fas fa-star-half-alt"></i>':'<i class="far fa-star"></i>');
                let schoolsPreview = service.schools.slice(0,2).join(', ')+(service.schools.length>2?'...':'');
                let areasPreview = service.areas_covered ? service.areas_covered.split(',').slice(0,2).join(', ') : '';
                if(service.areas_covered && service.areas_covered.split(',').length>2) areasPreview += '...';
                let firstImage = (service.images && service.images.length) ? service.images[0].image_path : 'assets/default-car.jpg';
                let imageCount = service.images ? service.images.length : 0;
                let commentsCount = service.comments_count || 0;
                let formattedComments = formatCount(commentsCount);
                html += `<div class="post-card" data-id="${service.service_id}">
                    ${currentUserLogged ? `<div class="card-header-fav"><button class="favorite-btn ${favClass}" onclick="toggleFavorite(${service.service_id}, this)"><i class="${favIcon}"></i></button></div>` : ''}
                    <img src="${firstImage}" class="service-image" onclick="openImageGallery(${service.service_id})" onerror="this.src='assets/default-car.jpg';">
                    ${imageCount>1 ? `<span class="badge bg-dark position-absolute top-0 start-0 m-2">${imageCount} ${t.photos || 'photos'}</span>` : ''}
                    <h5 class="mt-2">${escapeHtml(service.service_name)}</h5>
                    <div class="rating-display"><span>${starsHtml}</span><span>(${avg}/5 · ${service.total_ratings} ${t.ratings || 'ratings'})</span></div>
                    <div class="info"><i class="fas fa-id-card"></i> ${escapeHtml(service.reg_no)}</div>
                    <div class="info"><i class="fas fa-car"></i> ${escapeHtml(service.vehicle_type)}</div>
                    <div class="info"><i class="fas fa-tag"></i> ${escapeHtml(service.service_type)}</div>
                    <div class="info"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(service.province)}, ${escapeHtml(service.district)}</div>
                    <div class="info"><i class="fas fa-home"></i> ${escapeHtml(service.home_town)}</div>
                    <div class="info"><i class="fas fa-globe"></i> ${escapeHtml(areasPreview)}</div>
                    <div class="info"><i class="fas fa-school"></i> ${escapeHtml(schoolsPreview)}</div>
                    <div class="button-group">
                        <button class="see-more-btn" onclick="viewDetails(${service.service_id})"><i class="fas fa-eye"></i> ${t.see_more || 'See More'}</button>
                        <button class="view-comments-btn" onclick="openViewCommentsModelComp(${service.service_id})"><i class="fas fa-comments"></i> ${formattedComments} ${t.comments || 'Comments'}</button>
                        ${currentUserLogged ? `<button class="report-btn" onclick="openReportModal(${service.service_id})"><i class="fas fa-flag"></i> ${t.report || 'Report'}</button>` : ''}
                    </div>
                    ${currentUserLogged ? `
                    <div class="button-group mt-2">
                        <button class="feedback-btn" onclick="openRatingModelComp(${service.service_id},'${(service.service_name||'').replace(/'/g, "\\'")}',${service.user_rating||0})"><i class="fas fa-star"></i> ${t.rate || 'Rate'}</button>
                        <button class="comment-btn" onclick="openCommentModelComp(${service.service_id})"><i class="fas fa-comment"></i> ${t.comment || 'Comment'}</button>
                    </div>` : ''}
                </div>`;
            });
            container.innerHTML = html;
        } else container.innerHTML = `<div class="empty-state"><i class="fas fa-box-open"></i><h4>${t.no_services || 'No Services Found'}</h4></div>`;
    }).catch(() => document.getElementById('postsContainer').innerHTML=`<div class="empty-state">${t.error_loading || 'Error loading'}</div>`);
}

// Reset
document.getElementById('reset-filters').onclick = () => {
    document.getElementById('filter-province').value = '';
    populateDistricts('');
    document.getElementById('filter-service-type').value = '';
    document.getElementById('search-input').value = '';
    loadServices();
};

districtSelect.addEventListener('change', loadServices);
document.getElementById('filter-service-type').addEventListener('change', loadServices);
document.getElementById('search-btn').onclick = loadServices;
document.getElementById('search-input').addEventListener('keypress', e => {
    if (e.key === 'Enter') loadServices();
});

function toggleFavorite(serviceId, btn) {
    if(!currentUserLogged){ alert(t.login_to_favorite || 'Login to favorite'); return; }
    let fd = new FormData(); fd.append('service_id', serviceId);
    fetch('includes/toggle_favorite.php', {method:'POST', body:fd}).then(res=>res.json()).then(data=>{
        if(data.success){
            let icon = btn.querySelector('i');
            if(data.action === 'added'){ icon.className='fas fa-heart'; btn.classList.add('favorited'); }
            else{ icon.className='far fa-heart'; btn.classList.remove('favorited'); }
        } else alert(data.message);
    });
}

function openReportModal(serviceId) {
    if (!currentUserLogged) { alert(t.login_to_report || 'Please login to report'); return; }
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
                    html += `<div class="form-check">
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
                alert(t.failed_load_report_options || 'Failed to load report options');
            }
        });
}

document.getElementById('submitReportBtn').addEventListener('click', function() {
    let selected = [];
    document.querySelectorAll('.report-option:checked').forEach(cb => { selected.push(cb.value); });
    if (selected.length === 0) { document.getElementById('reportMessage').innerHTML = t.please_select_reason || 'Please select at least one reason.'; return; }
    if (!confirm(t.confirm_report || 'Are you 100% sure? This action cannot be undone.')) { return; }
    let customReason = document.getElementById('customReason').value;
    let fd = new FormData();
    fd.append('service_id', currentReportServiceId);
    fd.append('selected_options', JSON.stringify(selected));
    fd.append('custom_reason', customReason);
    fetch('includes/submit_report.php', {method:'POST', body:fd})
    .then(res => res.json())
    .then(data => {
        if (data.success) { reportModal.hide(); alert(t.report_success || 'Report submitted. Thank you.'); }
        else { document.getElementById('reportMessage').innerHTML = data.message || 'Submission failed.'; }
    });
});

loadServices();
</script>
</body>
</html>