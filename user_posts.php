<?php
include "includes/db_connection.php";

$Page_Name = "User Posts Management";

$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1;

if (!$isLogged) {
    header("Location: log_in.php");
    exit();
}

// Admin check
$adminCheck = $conn->prepare("SELECT user_type FROM users WHERE user_id = ?");
$adminCheck->bind_param("i", $user_id);
$adminCheck->execute();
$res = $adminCheck->get_result();
if ($res->num_rows === 0 || $res->fetch_assoc()['user_type'] !== 'admin') {
    die("Access denied. Admins only.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin - Post Management</title>
    <style>
    body {
  font-family: 'Inter', sans-serif;
  background: #0f0f13;
  min-height: 100vh;
   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.admin-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }

/* ── Page Header ── */
.admin-page-header {
  background: #1a1a24;
  border: 1px solid #2a2a38;
  border-radius: 20px;
  padding: 1rem 1.8rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}
.admin-page-header h2 { color: #e2e8f0; font-weight: 700; margin: 0; font-size: 1.3rem; }

/* ── Filters ── */
.admin-filters-section {
  background: #1a1a24;
  border: 1px solid #2a2a38;
  border-radius: 20px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.admin-filter-row  { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; }
.admin-action-row  { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
.admin-filter-group { flex: 1; min-width: 150px; }

.admin-filter-group label {
  color: #a0aec0;
  font-weight: 500;
  display: block;
  font-size: 0.78rem;
  margin-bottom: 0.4rem;
  text-transform: uppercase;
  letter-spacing: .04em;
}
.admin-filter-group select,
.admin-filter-group input {
  background: #0f0f18;
  border: 1px solid #2a2a38;
  border-radius: 50px;
  padding: 0.55rem 1rem;
  width: 100%;
  color: #e2e8f0;
  font-size: 0.85rem;
  outline: none;
  transition: .2s;
}
.admin-filter-group select:focus,
.admin-filter-group input:focus {
  border-color: #7c5cfc;
  box-shadow: 0 0 0 3px rgba(124,92,252,.15);
}
.admin-filter-group select option { background: #1a1a24; }

.admin-search-group { flex: 2; min-width: 250px; }
.admin-search-wrapper { position: relative; width: 100%; }
.admin-search-wrapper input {
  width: 100%;
  padding: .6rem 3rem .6rem 1.2rem;
  background: #0f0f18;
  border: 1px solid #2a2a38;
  border-radius: 50px;
  color: #e2e8f0;
  font-size: .9rem;
  outline: none;
  box-sizing: border-box;
  transition: .2s;
}
.admin-search-wrapper input::placeholder { color: #4a5568; font-size: .85rem; }
.admin-search-wrapper input:focus { border-color: #7c5cfc; }
.admin-search-wrapper button {
  position: absolute; right: 4px; top: 50%; transform: translateY(-50%);
  background: #7c5cfc; border: none; color: #fff;
  font-size: 1rem; cursor: pointer; padding: .4rem .9rem;
  height: calc(100% - 8px);
  border-radius: 0 50px 50px 0;
  display: flex; align-items: center; justify-content: center;
  transition: .2s;
}
.admin-search-wrapper button:hover { background: #6b4edf; }

.admin-reset-btn {
  background: transparent;
  border: 1px solid #2a2a38;
  border-radius: 50px;
  color: #a0aec0;
  padding: .55rem 1.4rem;
  white-space: nowrap;
  cursor: pointer;
  font-size: .85rem;
  transition: .2s;
}
.admin-reset-btn:hover { border-color: #7c5cfc; color: #7c5cfc; }
.admin-status-filter { min-width: 150px; }

/* ── Cards ── */
.admin-posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}
.admin-post-card {
  background: #1a1a24;
  border: 1px solid #2a2a38;
  border-radius: 20px;
  padding: 1.1rem;
  position: relative;
  transition: .25s;
  overflow: hidden;
}
.admin-post-card:hover {
  transform: translateY(-5px);
  border-color: #7c5cfc;
  box-shadow: 0 16px 40px rgba(124,92,252,.15);
}

.admin-service-image {
  width: 100%; height: 190px;
  object-fit: cover;
  border-radius: 14px;
  cursor: pointer;
  margin-bottom: .9rem;
  background: #0f0f18;
}
.badge.bg-dark {
  background: rgba(0,0,0,.65) !important;
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 50px;
  font-size: .7rem;
  backdrop-filter: blur(6px);
}

/* Status badges */
.badge.bg-warning { background: #2d2200 !important; color: #fbbf24 !important; border: 1px solid #4a3800; }
.badge.bg-success { background: #0d2e23 !important; color: #34d399 !important; border: 1px solid #164d3a; }
.badge.bg-danger  { background: #2d1515 !important; color: #f87171 !important; border: 1px solid #4a2020; }
.admin-status-badge { font-size: .72rem; padding: 3px 10px; border-radius: 50px; }

.admin-post-card h5 { font-size: 1rem; font-weight: 600; color: #e2e8f0; margin: .4rem 0; }

.admin-rating-display {
  display: flex; align-items: center; gap: 6px;
  margin: .3rem 0; font-size: .78rem; color: #f59e0b;
}
.admin-rating-display span:last-child { color: #718096; }

.admin-info { font-size: .8rem; color: #718096; margin: .25rem 0; display: flex; align-items: center; gap: 7px; }
.admin-info i { width: 16px; color: #7c5cfc; text-align: center; }

/* ── Action Buttons ── */
.admin-button-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: .85rem;
}

.admin-btn-row {
  display: flex;
  gap: 6px;
}

.admin-btn {
  flex: 1;
  border: none;
  border-radius: 30px;
  padding: 8px 6px;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
  font-size: .75rem;
  white-space: nowrap;
  transition: .15s;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
}
.admin-btn:hover { opacity: .85; transform: translateY(-1px); }

.admin-see-more-btn      { background: #7c5cfc; color: #fff; }
.admin-view-comments-btn { background: #1e3a5f; color: #60a5fa; border: 1px solid #1e4a8a; }
.admin-view-reports-btn  { background: #2d2200; color: #fbbf24; border: 1px solid #4a3800; }
.admin-see-user-btn      { background: #1e1b3a; color: #a78bfa; border: 1px solid #2d2860; }
.admin-message-btn       { background: #0e2d35; color: #22d3ee; border: 1px solid #0e4a5a; }
.admin-approve-btn       { background: #0d2e23; color: #34d399; border: 1px solid #164d3a; }
.admin-reject-btn        { background: #2d1515; color: #f87171; border: 1px solid #4a2020; }
.admin-delete-btn        { background: #3a0a0a; color: #fca5a5; border: 1px solid #600f0f; }

/* ── Empty State ── */
.admin-empty-state {
  text-align: center; padding: 3rem;
  background: #1a1a24;
  border: 1px solid #2a2a38;
  border-radius: 20px;
  color: #4a5568;
}

/* ── All Modals ── */
.modal-content {
  background: #1a1a24 !important;
  border: 1px solid #2a2a38 !important;
  border-radius: 20px !important;
  color: #e2e8f0 !important;
}
.modal-header { border-bottom: 1px solid #2a2a38 !important; }
.modal-footer { border-top: 1px solid #2a2a38 !important; }
.modal-header .btn-close        { filter: invert(1) opacity(.6); }
.modal-header .btn-close-white  { filter: invert(1) opacity(.8); }

.modal-header.bg-info    { background: #0e2d35 !important; }
.modal-header.bg-warning { background: #2d2200 !important; }
.modal-header.bg-primary { background: #1e1b3a !important; }
.modal-header.bg-danger  { background: #2d1515 !important; }
.modal-header.bg-success { background: #0d2e23 !important; }
.modal-header.text-white h5,
.modal-header.text-dark h5 { color: #e2e8f0 !important; }

.form-control {
  background: #0f0f18 !important;
  border: 1px solid #2a2a38 !important;
  color: #e2e8f0 !important;
  border-radius: 12px !important;
}
.form-control:focus {
  border-color: #7c5cfc !important;
  box-shadow: 0 0 0 3px rgba(124,92,252,.15) !important;
}
.form-control::placeholder { color: #4a5568 !important; }

/* ── Comment & Report items ── */
.admin-comment-item {
  background: #0f0f18;
  border-radius: 14px;
  padding: 1rem;
  margin-bottom: 1rem;
  border-left: 3px solid #7c5cfc;
}
.admin-comment-item strong { color: #e2e8f0; }
.admin-comment-item small  { color: #4a5568; }
.admin-comment-item div    { color: #a0aec0; margin-top: .3rem; font-size: .9rem; }

.admin-report-item {
  background: #0f0f18;
  border-radius: 14px;
  padding: 1rem;
  margin-bottom: 1rem;
  border-left: 3px solid #fbbf24;
}
.admin-report-item strong { color: #e2e8f0; }
.admin-report-item small  { color: #4a5568; }
.admin-report-item div    { color: #a0aec0; margin-top: .3rem; font-size: .9rem; }

/* ── User Details Table ── */
.table { color: #e2e8f0 !important; }
.table-bordered { border-color: #2a2a38 !important; }
.table-bordered th,
.table-bordered td { border-color: #2a2a38 !important; }
.table th { background: #0f0f18; color: #a0aec0; font-weight: 500; font-size: .82rem; }
.table td { color: #e2e8f0; font-size: .85rem; }

/* ── Message Bubbles ── */
.bg-primary.text-white { background: #7c5cfc !important; }
.bg-light {
  background: #1e1b3a !important;
  color: #000000 !important;
  border: 1px solid #2d2860 !important;
}
.text-muted { color: #4a5568 !important; }

/* ── Bootstrap overrides ── */
.btn-secondary { background: #1a1a24 !important; border-color: #2a2a38 !important; color: #a0aec0 !important; }
.btn-primary   { background: #7c5cfc !important; border-color: #7c5cfc !important; color: #fff !important; }
.btn-danger    { background: #dc2626 !important; border-color: #dc2626 !important; }
.btn-light     { background: #2a2a38 !important; border-color: #3a3a50 !important; color: #a0aec0 !important; }
.text-white    { color: #e2e8f0 !important; }
.text-dark     { color: #e2e8f0 !important; }
.text-center.p-4 { color: #718096; }
.text-danger   { color: #f87171 !important; }

.download-docs-btn {
            flex: 1; border: none; border-radius: 30px; padding: 8px 5px; font-weight: 600; font-size: .78rem;
            text-align: center; cursor: pointer; transition: .15s; background: #2d3748; color: #e2e8f0; border: 1px solid #4a5568;
        }
        .download-docs-btn:hover { opacity: .85; transform: translateY(-1px); background: #4a5568; }
 

        .admin-hold-btn { background: #3a0a0a; color: #fca5a5; border: 1px solid #600f0f; }

        .admin-status-history-btn {
    background: #2d3748; /* අඳුරු අළු */
    color: #e2e8f0;
    border: 1px solid #4a5568;
}

@media (max-width: 768px) {
  .admin-filter-row, .admin-action-row { flex-direction: column; align-items: stretch; }
  .admin-search-group { width: 100%; }
}
    </style>
</head>
<body>
<?php include "side_bar.php"; ?>
<?php include "stars_bg.php"; ?>
<div class="admin-container">
    <div class="admin-page-header">
        <h2><i class="fas fa-tasks"></i> Post Management</h2>
    </div>

    <!-- Filters section -->
    <div class="admin-filters-section">
        <div class="admin-filter-row">
            <div class="admin-filter-group">
                <label><i class="fas fa-map-marker-alt"></i> Province</label>
                <select id="filter-province">
                    <option value="">All</option>
                    <option>Western</option><option>Central</option><option>Southern</option>
                    <option>Northern</option><option>Eastern</option><option>North Western</option>
                    <option>North Central</option><option>Uva</option><option>Sabaragamuwa</option>
                </select>
            </div>
            <div class="admin-filter-group">
                <label><i class="fas fa-building"></i> District</label>
                <select id="filter-district">
                    <option value="">All</option>
                </select>
            </div>
            <div class="admin-filter-group admin-status-filter">
                <label><i class="fas fa-flag"></i> Status</label>
                <select id="filter-status">
                    <option value="all" selected>All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="admin-action-row">
            <div class="admin-filter-group admin-search-group">
                <label><i class="fas fa-search"></i> Search</label>
                <div class="admin-search-wrapper">
                    <input type="text" id="search-input" placeholder="Search by name, area, school, phone or registration number...">
                    <button type="button" id="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div>
                <button type="button" id="reset-filters" class="admin-reset-btn"><i class="fas fa-undo-alt"></i> Reset</button>
            </div>
        </div>
    </div>

    <div class="admin-posts-grid" id="postsContainer">
        <div class="admin-empty-state">Loading posts...</div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fas fa-envelope"></i> Messages with User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="messageList" style="max-height:400px; overflow-y:auto;"></div>
      <div class="modal-footer">
        <textarea id="newMessageText" class="form-control mb-2" rows="2" placeholder="Type reply..."></textarea>
        <button class="btn btn-primary" id="sendMessageBtn"><i class="fas fa-paper-plane"></i> Send</button>
      </div>
    </div>
  </div>
</div>

<!-- View Reports Modal -->
<div class="modal fade" id="viewReportsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="fas fa-flag"></i> Reports</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="reportsList"></div>
    </div>
  </div>
</div>

<!-- View User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-user"></i> User Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="userDetailsContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Post Modal (with password) -->
<div class="modal fade" id="deletePostModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-trash-alt"></i> Delete Post</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="fw-bold">This action is irreversible. Enter your admin password to confirm.</p>
        <div class="mb-3">
          <label for="deletePassword" class="form-label">Admin Password</label>
          <input type="password" class="form-control" id="deletePassword" placeholder="Enter password">
        </div>
        <div id="deleteMessage" class="text-danger mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="fas fa-trash"></i> Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Status History Modal -->
<div class="modal fade" id="statusHistoryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fas fa-history"></i> Post Management History</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="statusHistoryContent">
        <!-- History will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- ========== COMPONENT INCLUDES ========== -->
<?php include "components/see_more_modal.php"; ?>
<?php include "components/comment_model.php"; ?>
<?php include "components/view_comments_model.php"; ?>
<?php include "components/rating_model.php"; ?>
<?php include "components/image_viewing_model.php"; ?>   <!-- ✅ Image viewer component added -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const currentUserId = <?php echo $user_id; ?>;
const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
const viewReportsModal = new bootstrap.Modal(document.getElementById('viewReportsModal'));
const userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
const deletePostModal = new bootstrap.Modal(document.getElementById('deletePostModal'));
// ✅ imageViewerModal is now provided by the component, so we don't declare it here

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;');
}

// YouTube-style number formatting
function formatCount(count) {
    if (count === null || count === undefined) return '0';
    if (count < 1000) return count.toString();
    if (count < 1000000) return (count / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
    if (count < 1000000000) return (count / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
    return (count / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
}

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
const provinceSelect = document.getElementById('filter-province');
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
provinceSelect.addEventListener('change', function() {
    populateDistricts(this.value);
    loadPosts();
});
populateDistricts('');

let currentDeletePostId = null;

function loadPosts() {
    let status = document.getElementById('filter-status').value;
    let province = document.getElementById('filter-province').value;
    let district = document.getElementById('filter-district').value;
    let search = document.getElementById('search-input').value.trim();

    let params = new URLSearchParams({
        action: 'admin_fetch',
        status: status,
        province: province,
        district: district,
        search: search
    });

    fetch(`includes/add_and_edit_post_backend.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('postsContainer');
            if (data.success && data.posts.length) {
                let html = '';
                data.posts.forEach(post => {
                    let statusBadge = '';
if (post.status === 'pending') {
    if (post.edited_after_approval == 1) {
        statusBadge = '<span class="badge bg-warning text-dark admin-status-badge">Pending - Edited</span>';
    } else {
        statusBadge = '<span class="badge bg-warning text-dark admin-status-badge">Pending</span>';
    }
} else if (post.status === 'approved') {
    statusBadge = '<span class="badge bg-success admin-status-badge">Approved</span>';
} else if (post.status === 'rejected') {
    statusBadge = '<span class="badge bg-danger admin-status-badge">Rejected</span>';
} else if (post.status === 'hold') {   // ✅ අලුතෙන් එකතු කරන්න
    statusBadge = '<span class="badge bg-danger admin-status-badge">Hold For an Investigation</span>';
}

                    let firstImage = (post.images && post.images.length) ? post.images[0].image_path : 'assets/default-car.jpg';
                    let imageCount = post.images ? post.images.length : 0;

                    let avg = post.avg_rating ? parseFloat(post.avg_rating) : 0;
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (avg >= i) starsHtml += '<i class="fas fa-star"></i>';
                        else if (avg >= i - 0.5) starsHtml += '<i class="fas fa-star-half-alt"></i>';
                        else starsHtml += '<i class="far fa-star"></i>';
                    }
                    let ratingInfo = `<span class="admin-rating-display"><span>${starsHtml}</span><span>(${avg.toFixed(1)}/5 · ${post.total_ratings || 0} ratings)</span></span>`;
                    let reportCount = post.report_count || 0;
                    let commentsCount = post.comments_count || 0;
                    let formattedComments = formatCount(commentsCount);

                    html += `
                    <div class="admin-post-card" data-id="${post.service_id}">
                        ${statusBadge ? `<div class="position-absolute top-0 start-0 m-2">${statusBadge}</div>` : ''}
                        <img src="${firstImage}" class="admin-service-image" onclick="openImageGallery(${post.service_id})" onerror="this.src='assets/default-car.jpg';">
                        ${imageCount > 1 ? `<span class="badge bg-dark position-absolute top-0 end-0 m-2">${imageCount} photos</span>` : ''}
                        <h5>${escapeHtml(post.service_name)}</h5>
                        ${ratingInfo}
                        <div class="admin-info"><i class="fas fa-user"></i> Owner: ${escapeHtml(post.owner)}</div>
                        <div class="admin-info"><i class="fas fa-id-card"></i> ${escapeHtml(post.reg_no)}</div>
                        <div class="admin-info"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(post.province)}, ${escapeHtml(post.district)}</div>
                        <div class="admin-info"><i class="fas fa-home"></i> ${escapeHtml(post.home_town)}</div>
                        <div class="admin-button-group">
    <div class="admin-btn-row">
        <button class="admin-btn admin-see-more-btn" onclick="viewDetails(${post.service_id})"><i class="fas fa-eye"></i> See More</button>
        <button class="admin-btn admin-view-comments-btn" onclick="openViewCommentsModelComp(${post.service_id})"><i class="fas fa-comments"></i> ${formattedComments} Comments</button>
        <button class="admin-btn admin-view-reports-btn" onclick="viewReports(${post.service_id})"><i class="fas fa-flag"></i> ${reportCount} Reports</button>
    </div>
    <div class="admin-btn-row">
        <button class="admin-btn admin-see-user-btn" onclick="viewUserDetails(${post.service_id})"><i class="fas fa-user-circle"></i> See User</button>
        <button class="admin-btn admin-see-more-btn" onclick="viewStatusHistory(${post.service_id})">
        <i class="fas fa-history"></i> Status History
    </button>
        <button class="admin-btn admin-message-btn" onclick="openMessageModal(${post.service_id})"><i class="fas fa-envelope"></i> Messages</button>
    </div>
    <div class="admin-btn-row">
    ${post.status !== 'approved' ? `<button class="admin-btn admin-approve-btn" onclick="changeStatus(${post.service_id}, 'approved')"><i class="fas fa-check"></i> Approve</button>` : ''}
    ${post.status !== 'rejected' ? `<button class="admin-btn admin-reject-btn" onclick="changeStatus(${post.service_id}, 'rejected')"><i class="fas fa-times"></i> Reject</button>` : ''}
    ${post.status !== 'hold' ? `<button class="admin-btn admin-hold-btn" onclick="changeStatus(${post.service_id}, 'hold')"><i class="fas fa-pause-circle"></i> Hold</button>` : ''}
    <button class="admin-btn admin-delete-btn" onclick="openDeleteModal(${post.service_id})"><i class="fas fa-trash"></i> Delete</button>
</div>
    <button class="download-docs-btn" onclick="downloadDocuments(${post.service_id})"><i class="fas fa-download"></i> Docs</button>
</div>
                    </div>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="admin-empty-state"><i class="fas fa-box-open"></i> No posts found.</div>';
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('postsContainer').innerHTML = '<div class="admin-empty-state">Error loading posts.</div>';
        });
}

async function downloadDocuments(serviceId) {
    Swal.fire({
        title: 'Preparing download...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    try {
        const response = await fetch(`includes/user_documents_funcs.php?action=download_documents&service_id=${serviceId}`);
        if (!response.ok) {
            let errorMsg = `HTTP ${response.status}`;
            try {
                const json = await response.json();
                errorMsg = json.message || errorMsg;
            } catch {}
            throw new Error(errorMsg);
        }
        const contentType = response.headers.get('Content-Type');
        if (contentType && contentType.includes('application/json')) {
            const json = await response.json();
            throw new Error(json.message || 'Unexpected JSON response');
        }

        let filename = `Service_${serviceId}_Documents.zip`;
        const disposition = response.headers.get('Content-Disposition');
        if (disposition) {
            const match = disposition.match(/filename="?(.+?)"?$/);
            if (match && match[1]) {
                filename = match[1];
            }
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        Swal.fire({ icon: 'success', title: 'Download started', timer: 1500, showConfirmButton: false });
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Could not download documents' });
    }
}

function changeStatus(serviceId, newStatus) {
    if (!confirm(`Change status to ${newStatus}?`)) return;
    let fd = new FormData();
    fd.append('action', 'change_status');
    fd.append('service_id', serviceId);
    fd.append('status', newStatus);
    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
        if (data.success) { loadPosts(); }
        else { alert(data.message); }
    });
}

if (typeof viewDetails === 'undefined') {
    window.viewDetails = function(id) { alert('Details feature not available. Please check see_more_modal.php.'); };
}

// Filter events
document.getElementById('filter-district').addEventListener('change', loadPosts);
document.getElementById('filter-status').addEventListener('change', loadPosts);
document.getElementById('search-btn').addEventListener('click', loadPosts);
document.getElementById('reset-filters').addEventListener('click', () => {
    document.getElementById('filter-province').value = '';
    populateDistricts('');
    document.getElementById('filter-status').value = 'all';
    document.getElementById('search-input').value = '';
    loadPosts();
});
document.getElementById('search-input').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') loadPosts();
});

// ----- Messages -----
let currentMessagePostId = null;
function openMessageModal(postId) {
    currentMessagePostId = postId;
    messageModal.show();
    loadMessages(postId);
}
function loadMessages(postId) {
    document.getElementById('messageList').innerHTML = '<p>Loading...</p>';
    fetch(`includes/post_messages.php?action=fetch&post_id=${postId}`)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            let html = '';
            data.messages.forEach(msg => {
                let align = msg.sender_id == currentUserId ? 'text-end' : 'text-start';
                let bg   = msg.sender_id == currentUserId ? 'bg-primary text-white' : 'bg-light';
                html += `<div class="${align} mb-2">
                    <div class="d-inline-block p-2 rounded ${bg}" style="max-width:80%;">
                        <small class="fw-bold">${escapeHtml(msg.sender_name)}</small><br>
                        ${escapeHtml(msg.message)}<br>
                        <small class="text-muted">${msg.created_at}</small>
                    </div>
                </div>`;
            });
            document.getElementById('messageList').innerHTML = html || '<p class="text-center">No messages yet.</p>';
        }
    });
}
document.getElementById('sendMessageBtn').addEventListener('click', () => {
    let text = document.getElementById('newMessageText').value.trim();
    if (!text) return;
    let fd = new FormData();
    fd.append('action', 'send');
    fd.append('post_id', currentMessagePostId);
    fd.append('message', text);
    fetch('includes/post_messages.php', { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newMessageText').value = '';
            loadMessages(currentMessagePostId);
        }
    });
});

// ----- Reports viewing -----
function viewReports(serviceId) {
    let modalBody = document.getElementById('reportsList');
    modalBody.innerHTML = '<p>Loading reports...</p>';
    viewReportsModal.show();
    fetch(`includes/fetch_reports.php?service_id=${serviceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.reports.length) {
                let html = '';
                data.reports.forEach(r => {
                    let reasons = (r.selected_options_text && r.selected_options_text.length) 
                        ? r.selected_options_text.join(', ') 
                        : 'No reasons specified';
                    let custom = r.custom_reason ? `<div class="mt-1"><strong>Details:</strong> ${escapeHtml(r.custom_reason)}</div>` : '';
                    html += `<div class="admin-report-item">
                        <div class="d-flex justify-content-between">
                            <strong><i class="fas fa-flag"></i> Report</strong>
                            <small>${new Date(r.created_at).toLocaleString()}</small>
                        </div>
                        <div class="mt-1"><strong>Reasons:</strong> ${escapeHtml(reasons)}</div>
                        ${custom}
                    </div>`;
                });
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="text-center p-4">No reports yet.</div>';
            }
        })
        .catch(err => { modalBody.innerHTML = '<div class="text-center p-4 text-danger">Error loading reports.</div>'; });
}

const statusHistoryModal = new bootstrap.Modal(document.getElementById('statusHistoryModal'));

async function viewStatusHistory(serviceId) {
    const contentDiv = document.getElementById('statusHistoryContent');
    contentDiv.innerHTML = '<p class="text-center">Loading...</p>';
    statusHistoryModal.show();

    try {
        const response = await fetch(`includes/fetch_status_history.php?service_id=${serviceId}`);
        const data = await response.json();

        if (data.success && data.history.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
            html += '<thead><tr><th>#</th><th>Action</th><th>Admin ID</th><th>Admin Name</th><th>Date/Time</th></tr></thead><tbody>';
            data.history.forEach((entry, index) => {
                const actionBadge = entry.action === 'approved' ? '<span class="badge bg-success">Approved</span>' :
                                    entry.action === 'rejected' ? '<span class="badge bg-danger">Rejected</span>' :
                                    entry.action === 'hold' ? '<span class="badge bg-warning text-dark">Hold</span>' : entry.action;
                html += `<tr>
                    <td>${index+1}</td>
                    <td>${actionBadge}</td>
                    <td>${entry.admin_id}</td>
                    <td>${escapeHtml(entry.fullname)} (${escapeHtml(entry.username)})</td>
                    <td>${new Date(entry.changed_at).toLocaleString()}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = '<div class="text-center p-3">No status change history found.</div>';
        }
    } catch (error) {
        contentDiv.innerHTML = '<div class="text-center p-3 text-danger">Error loading history.</div>';
    }
}

// ----- View User Details -----
function viewUserDetails(serviceId) {
    let modalBody = document.getElementById('userDetailsContent');
    modalBody.innerHTML = '<p class="text-center">Loading...</p>';
    userDetailsModal.show();
    fetch(`includes/add_and_edit_post_backend.php?action=get_user&service_id=${serviceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.user) {
                const u = data.user;
                modalBody.innerHTML = `
                    <table class="table table-bordered table-sm">
                        <tr><th>User ID</th><td>${u.user_id}</td></tr>
                        <tr><th>Username</th><td>${escapeHtml(u.username)}</td></tr>
                        <tr><th>Full Name</th><td>${escapeHtml(u.fullname)}</td></tr>
                        <tr><th>Mobile</th><td>${escapeHtml(u.mobile)}</td></tr>
                        <tr><th>Email</th><td>${escapeHtml(u.email)}</td></tr>
                        <tr><th>NIC</th><td>${escapeHtml(u.nic)}</td></tr>
                        <tr><th>Province</th><td>${escapeHtml(u.province)}</td></tr>
                        <tr><th>District</th><td>${escapeHtml(u.district)}</td></tr>
                        <tr><th>Address</th><td>${escapeHtml(u.address)}</td></tr>
                        <tr><th>User Type</th><td>${escapeHtml(u.user_type)}</td></tr>
                        <tr><th>Registered</th><td>${new Date(u.created_at).toLocaleString()}</td></tr>
                    </table>
                `;
            } else {
                modalBody.innerHTML = '<div class="text-center text-danger">User details not found.</div>';
            }
        })
        .catch(err => { modalBody.innerHTML = '<div class="text-center text-danger">Error loading user.</div>'; });
}

// ----- Delete Post -----
function openDeleteModal(serviceId) {
    currentDeletePostId = serviceId;
    document.getElementById('deletePassword').value = '';
    document.getElementById('deleteMessage').innerHTML = '';
    deletePostModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    let password = document.getElementById('deletePassword').value.trim();
    if (!password) {
        document.getElementById('deleteMessage').innerText = 'Please enter your admin password.';
        return;
    }
    if (!confirm('Are you sure you want to permanently delete this post? This action cannot be undone.')) {
        return;
    }
    let fd = new FormData();
    fd.append('action', 'delete_post_admin');
    fd.append('service_id', currentDeletePostId);
    fd.append('password', password);
    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                deletePostModal.hide();
                alert('Post deleted successfully.');
                loadPosts();
            } else {
                document.getElementById('deleteMessage').innerText = data.message || 'Deletion failed.';
            }
        })
        .catch(err => {
            document.getElementById('deleteMessage').innerText = 'Server error.';
        });
});

// ✅ Image gallery functionality is now fully managed by the component.
// No duplicate openImageGallery, prev/next listeners, or imageViewerModal variable.

// Wrapper for component refresh calls
function loadServices() {
    loadPosts();
}

// Initial load
loadPosts();
</script>
</body>
</html>