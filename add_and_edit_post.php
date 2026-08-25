<?php
include "includes/db_connection.php";
$Page_Name = "Add/Edit Post";
$cookie_data = [];
if (isset($_COOKIE['abc'])) {
    $cookie_data = json_decode($_COOKIE['abc'], true);
}
$user_id = isset($cookie_data['user_id']) ? intval($cookie_data['user_id']) : 0;
$isLogged = (isset($cookie_data['islogged']) && $cookie_data['islogged'] == 1);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Paasal Riya - Manage Services</title>
    <style>
        body {
          font-family: 'Inter', sans-serif;
          background: #0f0f13;
          min-height: 100vh;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .page-header {
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
        .page-header h2 { color: #e2e8f0; font-weight: 700; margin: 0; font-size: 1.3rem; }
        .add-btn {
          background: #7c5cfc;
          color: #fff;
          border: none;
          border-radius: 50px;
          padding: 0.65rem 1.6rem;
          font-weight: 600;
          font-size: .9rem;
          transition: .2s;
          cursor: pointer;
        }
        .add-btn:hover { background: #6b4edf; transform: translateY(-2px); }
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .post-card {
          background: #1a1a24;
          border: 1px solid #2a2a38;
          border-radius: 20px;
          padding: 1.1rem;
          position: relative;
          transition: .25s;
          overflow: hidden;
        }
        .post-card:hover { transform: translateY(-5px); border-color: #7c5cfc; box-shadow: 0 16px 40px rgba(124,92,252,.15); }
        .card-actions {
          position: absolute;
          bottom: 0.5rem;
          left: 0.5rem;
          display: flex;
          gap: 7px;
          z-index: 2;
        }
        .edit-btn, .delete-btn {
          background: rgba(26,26,36,.85);
          border: 1px solid #2a2a38;
          border-radius: 50%;
          width: 34px; height: 34px;
          display: flex; align-items: center; justify-content: center;
          cursor: pointer;
          font-size: .85rem;
          transition: .2s;
          color: #a0aec0;
        }
        .edit-btn:hover   { background: #7c5cfc; border-color: #7c5cfc; color: #fff; }
        .delete-btn:hover { background: #dc2626; border-color: #dc2626; color: #fff; }
        .service-image { width: 100%; height: 190px; object-fit: cover; border-radius: 14px; cursor: pointer; margin-bottom: .9rem; background: #0f0f18; }
        .badge.bg-dark {
          background: rgba(0,0,0,.65) !important;
          border: 1px solid rgba(255,255,255,.1);
          border-radius: 50px;
          font-size: .7rem;
          backdrop-filter: blur(6px);
        }
        .badge.bg-warning { background: #2d2200 !important; color: #fbbf24 !important; border: 1px solid #4a3800; }
        .badge.bg-success { background: #0d2e23 !important; color: #34d399 !important; border: 1px solid #164d3a; }
        .badge.bg-danger  { background: #2d1515 !important; color: #f87171 !important; border: 1px solid #4a2020; }
        .status-badge     { font-size: .72rem; padding: 3px 10px; border-radius: 50px; }
        .post-card h5 { font-size: 1rem; font-weight: 600; color: #e2e8f0; margin: .4rem 0; }
        .rating-display { display: flex; align-items: center; gap: 7px; margin: .4rem 0; font-size: .78rem; color: #f59e0b; }
        .rating-display span:last-child { color: #718096; }
        .info { font-size: .8rem; color: #718096; margin: .25rem 0; display: flex; align-items: center; gap: 7px; }
        .info i { width: 16px; color: #7c5cfc; text-align: center; }
        .button-group { display: flex; gap: 7px; margin-top: .8rem; flex-wrap: wrap; }
        .see-more-btn, .view-comments-btn, .message-btn {
          flex: 1; border: none; border-radius: 30px; padding: 8px 5px; font-weight: 600; font-size: .78rem; text-align: center; cursor: pointer; transition: .15s;
        }
        .see-more-btn:hover, .view-comments-btn:hover, .message-btn:hover { opacity: .85; transform: translateY(-1px); }
        .see-more-btn      { background: #7c5cfc; color: #fff; }
        .view-comments-btn { background: #1e3a5f; color: #60a5fa; border: 1px solid #1e4a8a; }
        .message-btn       { background: #0e2d35; color: #22d3ee; border: 1px solid #0e4a5a; }
        .empty-state { text-align: center; padding: 3rem; background: #1a1a24; border: 1px solid #2a2a38; border-radius: 20px; color: #4a5568; }
        /* Modal styles */
        .modal-content { background: #1a1a24 !important; border: 1px solid #2a2a38 !important; border-radius: 20px !important; color: #e2e8f0 !important; }
        .modal-header { border-bottom: 1px solid #2a2a38 !important; }
        .modal-footer { border-top: 1px solid #2a2a38 !important; background: #1a1a24 !important; }
        .modal-header .btn-close { filter: invert(1) opacity(.6); }
        .modal-header.bg-primary { background: #1e1b3a !important; }
        .modal-header.bg-info    { background: #0e2d35 !important; }
        .modal-header.bg-danger  { background: #2d1515 !important; }
        .modal-header.bg-success { background: #0d2e23 !important; }
        .modal-header h5, .modal-header .modal-title { color: #e2e8f0 !important; }
        .modal-body[style*="background: #f1f5f9"] { background: #12121a !important; }
        .modal-body { background: #1a1a24; }
        .form-section { background: #12121a; border-radius: 16px; padding: 1.4rem; margin-bottom: 1.6rem; border: 1px solid #2a2a38; }
        .form-section h5 { font-weight: 600; color: #e2e8f0; margin-bottom: 1.1rem; display: flex; align-items: center; gap: .5rem; font-size: 1rem; }
        .form-section h5 i { color: #7c5cfc; }
        .form-control, .form-select { background: #0f0f18 !important; border: 1px solid #2a2a38 !important; color: #e2e8f0 !important; border-radius: 10px !important; }
        .form-control:focus, .form-select:focus { border-color: #7c5cfc !important; box-shadow: 0 0 0 3px rgba(124,92,252,.15) !important; }
        .form-control::placeholder { color: #4a5568 !important; }
        .form-select option { background: #1a1a24; }
        label.fw-semibold, label.fw-bold, .form-label { color: #a0aec0 !important; font-size: .85rem; }
        .dynamic-field-group { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .remove-field {
          background: #2d1515 !important; color: #f87171 !important; border: 1px solid #4a2020 !important; border-radius: 50% !important;
          width: 34px; height: 34px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
        }
        .remove-field:hover { background: #dc2626 !important; color: #fff !important; }
        .add-field-btn {
          background: #1e1b3a; border: 1px solid #2d2860; color: #a78bfa; border-radius: 40px; padding: 5px 14px; margin-top: 5px; cursor: pointer; font-size: .85rem; transition: .2s;
        }
        .add-field-btn:hover { background: #2d2860; }
        .schedule-entry { border: 1px solid #2a2a38; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; background: #0f0f18; }
        .image-preview-item, .mandatory-preview-item {
          position: relative;
          width: 120px;
          height: 120px;
          border-radius: 12px;
          overflow: hidden;
          border: 2px solid #2a2a38;
          display: inline-block;
          margin: 5px;
          vertical-align: top;
        }
        .image-preview-item img, .mandatory-preview-item img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          cursor: pointer;
        }
        .delete-opt-img {
          position: absolute; top: 2px; right: 2px; background: rgba(0,0,0,.75); color: #f87171; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; cursor: pointer; z-index: 2;
        }
        .mandatory-badge {
          background: #0d2e23; color: #34d399; border: 1px solid #164d3a; font-size: 9px; padding: 2px 6px; border-radius: 20px;
          position: absolute; bottom: 4px; left: 4px; z-index: 2;
        }
        .btn-primary   { background: #7c5cfc !important; border-color: #7c5cfc !important; color: #fff !important; }
        .btn-secondary { background: #1a1a24 !important; border-color: #2a2a38 !important; color: #a0aec0 !important; }
        .btn-danger    { background: #dc2626 !important; border-color: #dc2626 !important; color: #fff !important; }
        .btn-success   { background: #059669 !important; border-color: #059669 !important; }
        .btn-light     { background: #2a2a38 !important; border-color: #3a3a50 !important; color: #a0aec0 !important; }

        .download-docs-btn {
            flex: 1; border: none; border-radius: 30px; padding: 8px 5px; font-weight: 600; font-size: .78rem;
            text-align: center; cursor: pointer; transition: .15s; background: #2d3748; color: #e2e8f0; border: 1px solid #4a5568;
        }
        .download-docs-btn:hover { opacity: .85; transform: translateY(-1px); background: #4a5568; }
    </style>
</head>
<body>
<?php include "side_bar.php"; ?>
<?php include "stars_bg.php"; ?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-plus-circle"></i> My Services</h2>
        <button class="add-btn" id="openAddModalBtn"><i class="fas fa-plus"></i> Add New Service</button>
    </div>
    <div class="posts-grid" id="postsContainer"><div class="empty-state">Loading your services...</div></div>
</div>

<!-- Add/Edit Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background: #f1f5f9;">
                <form id="serviceForm" enctype="multipart/form-data">
                    <input type="hidden" name="service_id" id="service_id">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Service Name *</label>
                                <textarea name="service_name" id="service_name" rows="1" class="form-control" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Reg. No (Unique) *</label>
                                <input type="text" name="reg_no" id="reg_no" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Vehicle Type *</label>
                                <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                                    <option value="">Select</option>
                                    <option>Bus</option>
                                    <option>Van</option>
                                    <option>Three-wheeler</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Service Type *</label>
                                <select name="service_type" id="service_type" class="form-select" required>
                                    <option value="">Select</option>
                                    <option>School Transport</option>
                                    <option>Private Institute Transport</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Province *</label>
                                <select name="province" id="province" class="form-select" required>
                                    <option value="">Select</option>
                                    <option>Western</option><option>Central</option><option>Southern</option><option>Northern</option>
                                    <option>Eastern</option><option>North Western</option><option>North Central</option><option>Uva</option>
                                    <option>Sabaragamuwa</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">District *</label>
                                <select name="district" id="district" class="form-select" required>
                                    <option value="">Select Province first</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Your Home Town / Nearest Town *</label>
                                <input type="text" name="home_town" id="home_town" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Areas Covered (comma separated) *</label>
                                <input type="text" name="areas_covered" id="areas_covered" class="form-control" placeholder="e.g., Embilipitiya Town, Udawalawa Town, Ratnapura" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Address *</label>
                                <textarea name="address" id="address" rows="2" class="form-control" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Road Description (Optional)</label>
                                <textarea name="road_description" id="road_description" rows="2" class="form-control" placeholder="Describe the road condition, access points, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Owner & Crew -->
                    <div class="form-section">
                        <h5><i class="fas fa-user-tie"></i> Owner & Crew</h5>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Owner *</label>
                                <textarea name="owner" id="owner" rows="1" class="form-control" required></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Driver *</label>
                                <textarea name="driver" id="driver" rows="1" class="form-control" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Driver Reg.No *</label>
                                <input type="text" name="driver_reg_no" id="driver_reg_no" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Assistants</label>
                                <div id="assistants-container">
                                    <div class="dynamic-field-group">
                                        <textarea name="assistants[]" rows="1" class="form-control" placeholder="Assistant name"></textarea>
                                        <button type="button" class="remove-field btn btn-sm btn-danger">✖</button>
                                    </div>
                                </div>
                                <button type="button" class="add-field-btn" data-target="assistants">+ Add Assistant</button>
                            </div>
                        </div>
                    </div>

                    <!-- Schools / Institutes -->
                    <div class="form-section">
                        <h5><i class="fas fa-school"></i> Schools / Institutes (at least one) *</h5>
                        <div id="schools-container">
                            <div class="dynamic-field-group">
                                <input type="text" name="schools[]" class="form-control" placeholder="School / Institute name" required>
                                <button type="button" class="remove-field btn btn-sm btn-danger">✖</button>
                            </div>
                        </div>
                        <button type="button" class="add-field-btn" data-target="schools">+ Add School/Institute</button>
                    </div>

                    <!-- Schedule (Dynamic) -->
                    <div class="form-section">
                        <h5><i class="fas fa-clock"></i> Schedule <span class="text-danger">*</span></h5>
                        <p class="small mb-3">Add at least one schedule entry.</p>
                        <div id="schedules-container"></div>
                        <button type="button" class="add-field-btn" id="addScheduleBtn">+ Add Schedule</button>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-phone-alt"></i> Contact Details</h5>
                        <div class="mb-3">
                            <label class="fw-semibold">Telephone Numbers *</label>
                            <div id="telephones-container">
                                <div class="dynamic-field-group">
                                    <input type="text" name="telephones[]" class="form-control" placeholder="Phone number" required>
                                    <button type="button" class="remove-field btn btn-sm btn-danger">✖</button>
                                </div>
                            </div>
                            <button type="button" class="add-field-btn" data-target="telephones">+ Add Phone</button>
                        </div>
                        <div class="mb-3">
                            <label class="fw-semibold">Email Addresses</label>
                            <div id="emails-container">
                                <div class="dynamic-field-group">
                                    <input type="email" name="emails[]" class="form-control" placeholder="Email">
                                    <button type="button" class="remove-field btn btn-sm btn-danger">✖</button>
                                </div>
                            </div>
                            <button type="button" class="add-field-btn" data-target="emails">+ Add Email</button>
                        </div>
                        <div class="mb-3">
                            <label class="fw-semibold">Websites</label>
                            <div id="websites-container">
                                <div class="dynamic-field-group">
                                    <input type="url" name="websites[]" class="form-control" placeholder="Website">
                                    <button type="button" class="remove-field btn btn-sm btn-danger">✖</button>
                                </div>
                            </div>
                            <button type="button" class="add-field-btn" data-target="websites">+ Add Website</button>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="form-section">
                        <h5><i class="fas fa-images"></i> Images</h5>
                        <div id="mandatoryImagesSection" class="mb-3">
                            <label class="fw-bold">Mandatory Vehicle Images (6) *</label>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6"><label class="form-label">Front View *</label><input type="file" name="mandatory_front" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                                <div class="col-md-6"><label class="form-label">Back View *</label><input type="file" name="mandatory_back" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                                <div class="col-md-6"><label class="form-label">Left Side *</label><input type="file" name="mandatory_left" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                                <div class="col-md-6"><label class="form-label">Right Side *</label><input type="file" name="mandatory_right" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                                <div class="col-md-6"><label class="form-label">Seats / Interior 1 *</label><input type="file" name="mandatory_seats1" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                                <div class="col-md-6"><label class="form-label">Seats / Interior 2 *</label><input type="file" name="mandatory_seats2" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required><div class="mandatory-preview mt-2"></div></div>
                            </div>
                        </div>
                        <div id="existingImagesPreview" style="display:none;">
                            <label class="fw-bold">Current Mandatory Images</label>
                            <div id="mandatoryImagesPreview" class="row g-2 mb-3"></div>
                            <label class="fw-bold">Current Optional Images</label>
                            <div id="optionalImagesPreview" class="row g-2 mb-3"></div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Additional Images (Optional)</label>
                            <input type="file" name="optional_images[]" id="optionalImagesInput" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                            <div id="optionalPreview" class="mt-2 row g-2"></div>
                            <small>You can select multiple images. Max 5MB each.</small>
                        </div>
                    </div>

                    <!-- Documents Upload -->
                    <div class="form-section">
                        <h5><i class="fas fa-file-alt"></i> Documents Upload <span class="text-danger">*</span></h5>
                        <p class="small mb-2">Upload clear images of the following documents. At least one image is required.</p>
                        <ul class="small" id="requiredDocsList">
                            <!-- DB එකෙන් පුරවනු ලැබේ -->
                        </ul>
                        <label class="fw-semibold mt-2">Choose Files</label>
                        <input type="file" name="document_images[]" id="documentImagesInput" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                        <div id="documentPreview" class="mt-2 row g-2"></div>
                        <div id="existingDocumentImages" class="mt-2 row g-2"></div>
                    </div>

                    <!-- Description -->
                    <div class="form-section">
                        <h5><i class="fas fa-align-left"></i> Description</h5>
                        <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePostBtn">Save Service</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white"><h5>Confirm Delete</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Are you sure? All images will be removed.</p><input type="hidden" id="delete_post_id"></div>
            <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" id="confirmDeleteBtn">Delete</button></div>
        </div>
    </div>
</div>

<!-- Admin Messages Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-envelope"></i> Admin Messages</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageList" style="max-height:400px; overflow-y:auto;">
                <p>Loading...</p>
            </div>
            <div class="modal-footer">
                <textarea id="newMessageText" class="form-control mb-2" rows="2" placeholder="Type your message..."></textarea>
                <button class="btn btn-primary" id="sendMessageBtn"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== COMPONENT INCLUDES ========== -->
<?php include "components/comment_model.php"; ?>
<?php include "components/view_comments_model.php"; ?>
<?php include "components/see_more_modal.php"; ?>
<?php include "components/image_viewing_model.php"; ?>   <!-- ✅ image viewer component added -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── GLOBALS ──
const currentUserId = <?php echo $user_id; ?>;
let isEditMode = false;
let reqDocuments = [];

// ── HELPERS ──
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;');
}
function formatCount(count) {
    if (count === null || count === undefined) return '0';
    if (count < 1000) return count.toString();
    if (count < 1000000) return (count / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
    if (count < 1000000000) return (count / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
    return (count / 1000000000).toFixed(1).replace(/\.0$/, '') + 'B';
}

// ── DISTRICTS ──
const districtsByProvince = {
    'Western': ['Colombo', 'Gampaha', 'Kalutara'],
    'Central': ['Kandy', 'Matale', 'Nuwara Eliya'],
    'Southern': ['Galle', 'Matara', 'Hambantota'],
    'Northern': ['Jaffna', 'Kilinochchi', 'Mannar', 'Vavuniya', 'Mullaitivu'],
    'Eastern': ['Batticaloa', 'Ampara', 'Trincomalee'],
    'North Western': ['Kurunegala', 'Puttalam'],
    'North Central': ['Anuradhapura', 'Polonnaruwa'],
    'Uva': ['Badulla', 'Monaragala'],
    'Sabaragamuwa': ['Ratnapura', 'Kegalle']
};
const provinceSelect = document.getElementById('province');
const districtSelect = document.getElementById('district');
function updateDistricts() {
    const province = provinceSelect.value;
    districtSelect.innerHTML = '<option value="">Select District</option>';
    if (province && districtsByProvince[province]) {
        districtsByProvince[province].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }
}
provinceSelect.addEventListener('change', updateDistricts);

// ── DYNAMIC FIELDS ──
function addField(containerId, inputName, placeholder, required = false, isTextarea = false) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'dynamic-field-group';
    let fieldHtml = isTextarea ?
        `<textarea name="${inputName}[]" rows="1" class="form-control" placeholder="${placeholder}" ${required ? 'required' : ''}></textarea>` :
        `<input type="text" name="${inputName}[]" class="form-control" placeholder="${placeholder}" ${required ? 'required' : ''}>`;
    div.innerHTML = fieldHtml + '<button type="button" class="remove-field btn btn-sm btn-danger">✖</button>';
    container.appendChild(div);
    div.querySelector('.remove-field').addEventListener('click', () => div.remove());
}
document.querySelectorAll('.add-field-btn').forEach(btn => {
    if (btn.dataset.target) {
        btn.addEventListener('click', function() {
            const target = this.dataset.target;
            if (target === 'assistants') addField('assistants-container', 'assistants', 'Assistant name', false, true);
            else if (target === 'schools') addField('schools-container', 'schools', 'School/Institute name', true);
            else if (target === 'telephones') addField('telephones-container', 'telephones', 'Phone number', true);
            else if (target === 'emails') addField('emails-container', 'emails', 'Email', false);
            else if (target === 'websites') addField('websites-container', 'websites', 'Website', false);
        });
    }
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-field') && e.target.closest('.dynamic-field-group')) {
        e.target.closest('.dynamic-field-group').remove();
    }
});

// ── SCHEDULES ──
const scheduleContainer = document.getElementById('schedules-container');
let scheduleCounter = 0;
function createScheduleEntry(label='', place='', time='') {
    scheduleCounter++;
    const div = document.createElement('div');
    div.className = 'schedule-entry';
    div.innerHTML = `
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Label (optional)</label>
                <input type="text" name="schedule_label[]" class="form-control" value="${escapeHtml(label)}" placeholder="e.g., Morning Trip">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Place *</label>
                <input type="text" name="schedule_place[]" class="form-control" value="${escapeHtml(place)}" required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Time *</label>
                <input type="time" name="schedule_time[]" class="form-control" value="${escapeHtml(time)}" required>
            </div>
            <div class="col-md-1 d-flex align-items-end mb-2">
                <button type="button" class="remove-field btn btn-sm btn-danger remove-schedule">✖</button>
            </div>
        </div>
    `;
    div.querySelector('.remove-schedule').addEventListener('click', () => div.remove());
    scheduleContainer.appendChild(div);
}
document.getElementById('addScheduleBtn').addEventListener('click', () => createScheduleEntry());

// ── IMAGE PREVIEW CLICK TO OPEN IN NEW TAB ──
function openPreviewImage(src) {
    window.open(src, '_blank');
}

// ── MANDATORY IMAGE PREVIEWS ──
function attachMandatoryPreview() {
    document.querySelectorAll('.mandatory-file').forEach(input => {
        input.removeEventListener('change', mandatoryPreviewHandler);
        input.addEventListener('change', mandatoryPreviewHandler);
    });
}
function mandatoryPreviewHandler() {
    const previewDiv = this.parentNode.querySelector('.mandatory-preview');
    if (previewDiv) {
        previewDiv.innerHTML = '';
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imgSrc = e.target.result;
                previewDiv.innerHTML = `<div class="mandatory-preview-item" onclick="openPreviewImage('${imgSrc}')"><img src="${imgSrc}" alt="preview"></div>`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    }
}

// ── OPTIONAL IMAGES PREVIEW ──
document.getElementById('optionalImagesInput').addEventListener('change', function() {
    const preview = document.getElementById('optionalPreview');
    preview.innerHTML = '';
    if (this.files) {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imgSrc = e.target.result;
                preview.innerHTML += `<div class="image-preview-item" onclick="openPreviewImage('${imgSrc}')"><img src="${imgSrc}" alt="preview"></div>`;
            };
            reader.readAsDataURL(file);
        });
    }
});

// ── DOCUMENTS ──
function loadRequiredDocuments() {
    return fetch('includes/add_and_edit_post_backend.php?action=fetch_req_documents')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                reqDocuments = data.documents;
                document.getElementById('requiredDocsList').innerHTML = reqDocuments.map(d => `<li>${escapeHtml(d.document_name)}</li>`).join('');
            }
        });
}
document.getElementById('documentImagesInput').addEventListener('change', function() {
    const preview = document.getElementById('documentPreview');
    preview.innerHTML = '';
    if (this.files) {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imgSrc = e.target.result;
                preview.innerHTML += `<div class="image-preview-item" onclick="openPreviewImage('${imgSrc}')"><img src="${imgSrc}" alt="doc preview"></div>`;
            };
            reader.readAsDataURL(file);
        });
    }
});
function buildExistingDocumentImages(images) {
    const container = document.getElementById('existingDocumentImages');
    container.innerHTML = '';
    if (images && images.length) {
        images.forEach(img => {
            const path = img.image_path;
            container.innerHTML += `
                <div class="image-preview-item">
                    <img src="${path}" onclick="openPreviewImage('${path}')" alt="doc">
                    <button type="button" class="delete-opt-img delete-doc-img" data-doc-img-id="${img.id}">×</button>
                </div>`;
        });
    }
    document.querySelectorAll('.delete-doc-img').forEach(btn => {
        btn.addEventListener('click', function() {
            const imgId = this.dataset.docImgId;
            if (!confirm('Delete this document image?')) return;
            const fd = new FormData();
            fd.append('action', 'delete_document_image');
            fd.append('image_id', imgId);
            fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        editPost(document.getElementById('service_id').value);
                    } else {
                        alert(data.message);
                    }
                });
        });
    });
}

// ── MODALS ──
const serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
// ✅ imageViewerModal is now provided by the component, no need to declare here
const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

// ── LOAD POSTS ──
function loadPosts() {
    fetch('includes/add_and_edit_post_backend.php?action=fetch')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('postsContainer');
            if (data.success && data.posts.length) {
                let html = '';
                data.posts.forEach(post => {
                    let statusBadge = '';
if (post.status === 'pending') {
    if (post.edited_after_approval == 1) {
        statusBadge = '<span class="badge bg-warning text-dark status-badge">Pending - Edited</span>';
    } else {
        statusBadge = '<span class="badge bg-warning text-dark status-badge">Pending</span>';
    }
} else if (post.status === 'approved') {
    statusBadge = '<span class="badge bg-success status-badge">Approved</span>';
} else if (post.status === 'rejected') {
    statusBadge = '<span class="badge bg-danger status-badge">Rejected</span>';
} else if (post.status === 'hold') {
    statusBadge = '<span class="badge bg-danger status-badge">Hold For an Investigation</span>';
}
let avg = post.avg_rating || 0;
                    let stars = '';
                    for (let i = 1; i <= 5; i++) {
                        stars += i <= Math.round(avg) ? '<i class="fas fa-star"></i>' : (i - 0.5 <= avg ? '<i class="fas fa-star-half-alt"></i>' : '<i class="far fa-star"></i>');
                    }
                    let firstImage = (post.images && post.images.length) ? post.images[0].image_path : 'assets/default-car.jpg';
                    let imageCount = post.images ? post.images.length : 0;
                    let schoolsPreview = post.schools.slice(0, 2).join(', ') + (post.schools.length > 2 ? '...' : '');
                    html += `
                    <div class="post-card">
                        <div class="position-relative">
                            <div class="card-actions">
                                <button class="edit-btn" onclick="event.stopPropagation(); editPost(${post.service_id})"><i class="fas fa-edit"></i></button>
                                <button class="delete-btn" onclick="event.stopPropagation(); confirmDelete(${post.service_id})"><i class="fas fa-trash-alt"></i></button>
                            </div>
                            ${statusBadge ? `<div class="position-absolute top-0 start-0 m-2">${statusBadge}</div>` : ''}
                            <img src="${firstImage}" class="service-image" onclick="openImageGallery(${post.service_id})" onerror="this.src='assets/default-car.jpg';">
                            ${imageCount > 1 ? `<span class="badge bg-dark position-absolute top-0 end-0 m-2">${imageCount} photos</span>` : ''}
                        </div>
                        <h5>${escapeHtml(post.service_name)}</h5>
                        <div class="rating-display"><span>${stars}</span><span>(${avg}/5 · ${post.total_ratings} ratings)</span></div>
                        <div class="info"><i class="fas fa-id-card"></i> ${escapeHtml(post.reg_no)}</div>
                        <div class="info"><i class="fas fa-car"></i> ${escapeHtml(post.vehicle_type)}</div>
                        <div class="info"><i class="fas fa-tag"></i> ${escapeHtml(post.service_type)}</div>
                        <div class="info"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(post.province)}, ${escapeHtml(post.district)}</div>
                        <div class="info"><i class="fas fa-home"></i> ${escapeHtml(post.home_town)}</div>
                        <div class="info"><i class="fas fa-school"></i> ${escapeHtml(schoolsPreview)}</div>
                        <div class="button-group">
                            <button class="see-more-btn" onclick="viewDetails(${post.service_id})"><i class="fas fa-eye"></i> See More</button>
                            <button class="view-comments-btn" onclick="openViewCommentsModelComp(${post.service_id})"><i class="fas fa-comments"></i> ${formatCount(post.comments_count)} Comments</button>
                            <button class="message-btn" onclick="openMessageModal(${post.service_id})"><i class="fas fa-envelope"></i> Admin Messages</button>
                            <button class="download-docs-btn" onclick="downloadDocuments(${post.service_id})"><i class="fas fa-download"></i> Docs</button>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-box-open"></i><h4>No Services Yet</h4><p>Click "Add New Service" to begin.</p></div>';
            }
        }).catch(() => container.innerHTML = '<div class="empty-state">Error loading services</div>');
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

// ── ADD MODAL ──
document.getElementById('openAddModalBtn').addEventListener('click', async () => {
    isEditMode = false;
    document.getElementById('modalTitle').innerText = 'Add New Service';
    document.getElementById('serviceForm').reset();
    document.getElementById('service_id').value = '';
    ['assistants-container', 'schools-container', 'telephones-container', 'emails-container', 'websites-container'].forEach(id => {
        let cont = document.getElementById(id);
        let required = (id === 'schools-container' || id === 'telephones-container');
        let isTextarea = (id === 'assistants-container');
        let fieldHtml = isTextarea ? 
            `<textarea name="${id.split('-')[0]}[]" rows="1" class="form-control" placeholder="${required ? 'Required' : 'Optional'}" ${required ? 'required' : ''}></textarea>` :
            `<input type="text" name="${id.split('-')[0]}[]" class="form-control" placeholder="${required ? 'Required' : 'Optional'}" ${required ? 'required' : ''}>`;
        cont.innerHTML = `<div class="dynamic-field-group">${fieldHtml}<button type="button" class="remove-field btn btn-sm btn-danger">✖</button></div>`;
    });
    scheduleContainer.innerHTML = '';
    scheduleCounter = 0;
    createScheduleEntry();
    document.getElementById('mandatoryImagesSection').style.display = 'block';
    document.getElementById('existingImagesPreview').style.display = 'none';
    document.querySelectorAll('#mandatoryImagesSection .mandatory-file').forEach(input => {
        input.setAttribute('required', '');
    });
    document.querySelectorAll('.mandatory-preview').forEach(div => div.innerHTML = '');
    document.getElementById('optionalPreview').innerHTML = '';
    document.getElementById('documentImagesInput').value = '';
    document.getElementById('documentPreview').innerHTML = '';
    document.getElementById('existingDocumentImages').innerHTML = '';
    if (reqDocuments.length === 0) await loadRequiredDocuments();
    updateDistricts();
    attachMandatoryPreview();
    serviceModal.show();
});

// ── EDIT POST ──
function editPost(serviceId) {
    isEditMode = true;
    Promise.all([
        fetch(`includes/add_and_edit_post_backend.php?action=get&post_id=${serviceId}`).then(r=>r.json()),
        reqDocuments.length ? Promise.resolve() : loadRequiredDocuments()
    ]).then(([data]) => {
        if (data.success) {
            let s = data.post;
            document.getElementById('service_id').value = s.service_id;
            document.getElementById('service_name').value = s.service_name;
            document.getElementById('reg_no').value = s.reg_no;
            document.getElementById('vehicle_type').value = s.vehicle_type;
            document.getElementById('service_type').value = s.service_type;
            document.getElementById('owner').value = s.owner;
            document.getElementById('driver').value = s.driver;
            document.getElementById('driver_reg_no').value = s.driver_reg_no;
            document.getElementById('province').value = s.province;
            updateDistricts();
            document.getElementById('district').value = s.district;
            document.getElementById('home_town').value = s.home_town;
            document.getElementById('areas_covered').value = s.areas_covered;
            document.getElementById('address').value = s.address;
            document.getElementById('road_description').value = s.road_description || '';
            document.getElementById('description').value = s.description || '';

            scheduleContainer.innerHTML = '';
            scheduleCounter = 0;
            if (s.schedules && s.schedules.length) {
                s.schedules.forEach(sch => createScheduleEntry(sch.label, sch.place, sch.time));
            } else {
                createScheduleEntry();
            }

            function populate(containerId, values, nameAttr, requiredFlag, isTextarea = false) {
                let cont = document.getElementById(containerId);
                cont.innerHTML = '';
                if (values && values.length) {
                    values.forEach(v => {
                        let div = document.createElement('div');
                        div.className = 'dynamic-field-group';
                        let fieldHtml = isTextarea ?
                            `<textarea name="${nameAttr}[]" rows="1" class="form-control" ${requiredFlag ? 'required' : ''}>${escapeHtml(v)}</textarea>` :
                            `<input type="text" name="${nameAttr}[]" class="form-control" value="${escapeHtml(v)}" ${requiredFlag ? 'required' : ''}>`;
                        div.innerHTML = fieldHtml + '<button class="remove-field btn btn-sm btn-danger">✖</button>';
                        cont.appendChild(div);
                    });
                } else {
                    let div = document.createElement('div');
                    div.className = 'dynamic-field-group';
                    let fieldHtml = isTextarea ?
                        `<textarea name="${nameAttr}[]" rows="1" class="form-control" placeholder="${requiredFlag ? 'Required' : 'Optional'}" ${requiredFlag ? 'required' : ''}></textarea>` :
                        `<input type="text" name="${nameAttr}[]" class="form-control" placeholder="${requiredFlag ? 'Required' : 'Optional'}" ${requiredFlag ? 'required' : ''}>`;
                    div.innerHTML = fieldHtml + '<button class="remove-field btn btn-sm btn-danger">✖</button>';
                    cont.appendChild(div);
                }
            }
            populate('assistants-container', s.assistants, 'assistants', false, true);
            populate('schools-container', s.schools, 'schools', true);
            populate('telephones-container', s.telephones, 'telephones', true);
            populate('emails-container', s.emails, 'emails', false);
            populate('websites-container', s.websites, 'websites', false);

            document.getElementById('mandatoryImagesSection').style.display = 'none';
            document.getElementById('existingImagesPreview').style.display = 'block';

            document.querySelectorAll('#mandatoryImagesSection .mandatory-file').forEach(input => {
                input.removeAttribute('required');
            });

            document.querySelectorAll('.mandatory-preview').forEach(div => div.innerHTML = '');
            document.getElementById('optionalPreview').innerHTML = '';
            let mandatoryImages = s.images.filter(img => img.is_mandatory > 0);
            let optionalImages = s.images.filter(img => img.is_mandatory == 0);
            let mandatorySlots = {1:'Front',2:'Back',3:'Left',4:'Right',5:'Seats1',6:'Seats2'};
            let mandatoryHtml = '';
            for (let i = 1; i <= 6; i++) {
                let img = mandatoryImages.find(m => m.is_mandatory == i);
                let slotName = mandatorySlots[i];
                if (img) {
                    mandatoryHtml += `<div class="col-md-4 mb-2" data-mandatory-slot="${i}">
                        <div class="mandatory-preview-item">
                            <img src="${img.image_path}" onclick="openPreviewImage('${img.image_path}')" alt="${slotName}">
                            <span class="mandatory-badge">${slotName}</span>
                            <button type="button" class="delete-opt-img delete-mandatory-img" data-image-id="${img.image_id}" data-slot="${i}">×</button>
                        </div>
                        <div class="mt-1"><input type="file" name="mandatory_replace_${i}" class="form-control form-control-sm replace-mandatory-file" accept="image/jpeg,image/png,image/webp" style="display:none;"></div>
                    </div>`;
                } else {
                    mandatoryHtml += `<div class="col-md-4 mb-2" data-mandatory-slot="${i}">
                        <div class="alert alert-danger p-2">${slotName} image missing! Please upload.</div>
                        <input type="file" name="mandatory_replace_${i}" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required>
                        <div class="mandatory-preview mt-2"></div>
                    </div>`;
                }
            }
            document.getElementById('mandatoryImagesPreview').innerHTML = mandatoryHtml;
            let optionalHtml = '';
            optionalImages.forEach(img => {
                optionalHtml += `<div class="image-preview-item">
                    <img src="${img.image_path}" onclick="openPreviewImage('${img.image_path}')" alt="optional">
                    <button type="button" class="delete-opt-img" onclick="deleteOptionalImage(${img.image_id}, ${s.service_id})">×</button>
                </div>`;
            });
            document.getElementById('optionalImagesPreview').innerHTML = optionalHtml || '<div>No additional images</div>';

            document.querySelectorAll('.delete-mandatory-img').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!confirm('Delete this mandatory image? You must upload a replacement.')) return;
                    const imageId = this.dataset.imageId;
                    const slot = this.dataset.slot;
                    const fd = new FormData();
                    fd.append('action', 'delete_optional_image');
                    fd.append('image_id', imageId);
                    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const slotDiv = document.querySelector(`[data-mandatory-slot="${slot}"]`);
                            slotDiv.innerHTML = `<div class="alert alert-danger p-2">Mandatory image missing. Please upload.</div>
                                <input type="file" name="mandatory_replace_${slot}" class="form-control mandatory-file" accept="image/jpeg,image/png,image/webp" required>
                                <div class="mandatory-preview mt-2"></div>`;
                            attachMandatoryPreview();
                        } else alert(data.message);
                    });
                });
            });
            attachMandatoryPreview();

            document.getElementById('documentImagesInput').value = '';
            document.getElementById('documentPreview').innerHTML = '';
            fetch(`includes/add_and_edit_post_backend.php?action=get_document_images&service_id=${serviceId}`)
            .then(r => r.json())
            .then(docData => {
                if (docData.success) {
                    buildExistingDocumentImages(docData.images);
                }
            });

            document.getElementById('modalTitle').innerText = 'Edit Service';
            serviceModal.show();
        } else {
            alert('Failed to fetch service details');
        }
    });
}
function deleteOptionalImage(imageId, serviceId) {
    if (!confirm('Delete this image?')) return;
    let fd = new FormData();
    fd.append('action', 'delete_optional_image');
    fd.append('image_id', imageId);
    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) editPost(serviceId);
            else alert(data.message);
        });
}

// ── SAVE ──
document.getElementById('savePostBtn').addEventListener('click', () => {
    const scheduleEntries = document.querySelectorAll('#schedules-container .schedule-entry');
    if (scheduleEntries.length === 0) {
        alert('At least one schedule entry is required.');
        return;
    }
    let valid = true;
    scheduleEntries.forEach(entry => {
        const place = entry.querySelector('[name="schedule_place[]"]');
        const time = entry.querySelector('[name="schedule_time[]"]');
        if (!place.value.trim() || !time.value) valid = false;
    });
    if (!valid) {
        alert('Each schedule entry must have a place and time.');
        return;
    }
    const form = document.getElementById('serviceForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const docInput = document.getElementById('documentImagesInput');
    const existingDocImgs = document.querySelectorAll('#existingDocumentImages .delete-doc-img').length;
    if (docInput.files.length === 0 && existingDocImgs === 0) {
        alert('Please upload at least one document image.');
        return;
    }
    const formData = new FormData(form);
    formData.append('action', isEditMode ? 'edit' : 'add');
    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                serviceModal.hide();
                loadPosts();
            } else {
                alert(data.message || 'Operation failed');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Network error. Please try again.');
        });
});

// ── DELETE ──
function confirmDelete(postId) {
    document.getElementById('delete_post_id').value = postId;
    deleteModal.show();
}
document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    let fd = new FormData();
    fd.append('action', 'delete');
    fd.append('post_id', document.getElementById('delete_post_id').value);
    fetch('includes/add_and_edit_post_backend.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                deleteModal.hide();
                loadPosts();
            } else alert(data.message);
        });
});

// ── MESSAGES ──
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
                    <div class="d-inline-block p-2 rounded ${bg}" style="max-width:80%; color:black;">
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
        } else {
            alert(data.message);
        }
    });
});

// ✅ image viewer functions are now provided by the component, so we remove them from here.

// ── WRAPPER FOR COMPONENT REFRESH ──
function loadServices() {
    loadPosts();
}

// ── INIT ──
attachMandatoryPreview();
loadRequiredDocuments().then(() => loadPosts());
</script>
</body>
</html>