<?php
session_start();
include "Includes/db_connection.php";
include "Cookie_Managements/cookie_management.php";

$Page_Name = "Users Management";

// ---- Session Message Helper ----
$Session_Messages_Helper = [
    "edit_user_success" => [
        "class" => "session-success",
        "icon"  => "fa-check-circle"
    ],
    "edit_user_error" => [
        "class" => "session-error",
        "icon"  => "fa-times-circle"
    ]
];

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$user_type_filter = isset($_GET['user_type']) ? $_GET['user_type'] : '';
$creation_method_filter = isset($_GET['creation_method']) ? $_GET['creation_method'] : '';

// Sorting settings
$allowed_sorts = [
    'user_id'         => 'u.user_id',
    'username'        => 'u.username',
    'fullname'        => 'u.fullname',
    'user_type'       => 'u.user_type',
    'creation_method' => "COALESCE(ucm.method, 'Unknown')",
    'created_at'      => 'u.created_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'created_at';
$order = (isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC') ? 'ASC' : 'DESC';
$order_by = $allowed_sorts[$sort] . ' ' . $order;

// Edit modal handling
$edit_errors = [];
$edit_old_input = [];
$edit_modal_open = false;
$edit_user_id = 0;
$edit_user_original = [];

if (isset($_GET['edit_modal']) && $_GET['edit_modal'] == '1' && isset($_GET['edit_user_id'])) {
    $edit_modal_open = true;
    $edit_user_id = (int)$_GET['edit_user_id'];
    
    $stmt_original = $conn->prepare("SELECT username, fullname, mobile, email, nic, district, province, address, user_type FROM users WHERE user_id = ?");
    $stmt_original->bind_param("i", $edit_user_id);
    $stmt_original->execute();
    $result_original = $stmt_original->get_result();
    if ($result_original->num_rows === 1) {
        $edit_user_original = $result_original->fetch_assoc();
    }
    $stmt_original->close();

    if (isset($_SESSION['edit_errors'])) {
        $edit_errors = $_SESSION['edit_errors'];
        unset($_SESSION['edit_errors']);
    }
    if (isset($_SESSION['edit_old_input'])) {
        $edit_old_input = $_SESSION['edit_old_input'];
        unset($_SESSION['edit_old_input']);
    }
}

function edit_value($field, $edit_old_input, $edit_user_original) {
    return isset($edit_old_input[$field]) ? htmlspecialchars($edit_old_input[$field]) : 
           (isset($edit_user_original[$field]) ? htmlspecialchars($edit_user_original[$field]) : '');
}

// ---- Province-District mapping (Sri Lanka) ----
$province_district_map = [
    "Western"        => ["Colombo", "Gampaha", "Kalutara"],
    "Central"        => ["Kandy", "Matale", "Nuwara Eliya"],
    "Southern"       => ["Galle", "Matara", "Hambantota"],
    "Northern"       => ["Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu"],
    "Eastern"        => ["Batticaloa", "Ampara", "Trincomalee"],
    "North Western"  => ["Kurunegala", "Puttalam"],
    "North Central"  => ["Anuradhapura", "Polonnaruwa"],
    "Uva"            => ["Badulla", "Moneragala"],
    "Sabaragamuwa"   => ["Ratnapura", "Kegalle"]
];

// ---- Helper: build URL with all current parameters except page ----
function build_page_url($page, $extra_params = []) {
    $params = $_GET;
    unset($params['page']);
    $params = array_merge($params, $extra_params);
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

// ---- Helper: build sort link (resets to page 1) ----
function build_sort_url($column, $current_sort, $current_order) {
    $params = $_GET;
    unset($params['page']);
    $params['sort'] = $column;
    if ($column === $current_sort && $current_order === 'ASC') {
        $params['order'] = 'DESC';
    } else {
        $params['order'] = 'ASC';
    }
    return '?' . http_build_query($params);
}

// ---- Main user list query ----
$where_clauses = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_clauses[] = "(u.username LIKE ? OR u.email LIKE ? OR u.mobile LIKE ? OR u.nic LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

if (!empty($user_type_filter)) {
    $where_clauses[] = "u.user_type = ?";
    $params[] = $user_type_filter;
    $types .= 's';
}

if (!empty($creation_method_filter)) {
    if ($creation_method_filter === 'Unknown') {
        $where_clauses[] = "ucm.method IS NULL";
    } else {
        $where_clauses[] = "ucm.method = ?";
        $params[] = $creation_method_filter;
        $types .= 's';
    }
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

$count_sql = "SELECT COUNT(DISTINCT u.user_id) AS total FROM users u LEFT JOIN user_created_method ucm ON u.user_id = ucm.user_id $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$count_result = $stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt->close();

$data_sql = "SELECT u.user_id, u.username, u.fullname, u.mobile, u.email, u.nic, u.district, u.province, u.address, u.user_type, u.created_at, ucm.method AS creation_method 
             FROM users u 
             LEFT JOIN user_created_method ucm ON u.user_id = ucm.user_id 
             $where_sql 
             ORDER BY $order_by 
             LIMIT ? OFFSET ?";
$stmt = $conn->prepare($data_sql);
if (!empty($params)) {
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
} else {
    $params = [$limit, $offset];
    $types = 'ii';
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$user_types_result = $conn->query("SELECT DISTINCT user_type FROM users ORDER BY user_type");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png" sizes="32x32">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; overflow-x: hidden; }
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px; }

        .session-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            border-left: 5px solid;
        }
        .session-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        .session-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        
        /* Sortable header styling */
        .sortable {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
        .sortable:hover {
            text-decoration: underline;
        }
        .sort-icon {
            margin-left: 4px;
            font-size: 0.8em;
        }
        .pagination .page-link {
            cursor: pointer;
        }
    </style>
    <title>Paasal Riya - User Management</title>
</head>
<body>

<?php include "side_bar.php"; ?>

<div class="container">
    <h2 class="mb-4 text-white">User Management</h2>

    <?php
    foreach ($Session_Messages_Helper as $session_key => $settings) {
        if (isset($_SESSION[$session_key])) {
            $message = $_SESSION[$session_key];
            $class = $settings["class"] ?? "";
            $icon  = $settings["icon"] ?? "fa-info-circle";
            echo "<div class='session-message {$class}'>
                    <i class='fas {$icon}'></i> {$message}
                  </div>";
            unset($_SESSION[$session_key]);
        }
    }
    ?>

    <form method="GET" class="row g-3 mb-4">
        <!-- Preserve sort & order when filtering -->
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
        
        <div class="col-md-5">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by username, email, mobile or NIC..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>
        <div class="col-md-2">
            <select name="user_type" class="form-select">
                <option value="">All User Types</option>
                <?php while ($ut_row = $user_types_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($ut_row['user_type']) ?>" <?= $user_type_filter == $ut_row['user_type'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ut_row['user_type']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="creation_method" class="form-select">
                <option value="">All Creation Methods</option>
                <option value="Normal" <?= $creation_method_filter === 'Normal' ? 'selected' : '' ?>>Normal Sign Up</option>
                <option value="Custom" <?= $creation_method_filter === 'Custom' ? 'selected' : '' ?>>Created By an Admin</option>
                <option value="Unknown" <?= $creation_method_filter === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
        <div class="col-md-1">
            <a href="user_management.php" class="btn btn-outline-light w-100">Reset</a>
        </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <a href="<?= build_sort_url('user_id', $sort, $order) ?>" class="sortable text-white">
                                ID
                                <?php if ($sort === 'user_id'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= build_sort_url('username', $sort, $order) ?>" class="sortable text-white">
                                Username
                                <?php if ($sort === 'username'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= build_sort_url('fullname', $sort, $order) ?>" class="sortable text-white">
                                Full Name
                                <?php if ($sort === 'fullname'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Contact & NIC</th>
                        <th>Location</th>
                        <th>
                            <a href="<?= build_sort_url('user_type', $sort, $order) ?>" class="sortable text-white">
                                User Type
                                <?php if ($sort === 'user_type'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= build_sort_url('creation_method', $sort, $order) ?>" class="sortable text-white">
                                Creation Method
                                <?php if ($sort === 'creation_method'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= build_sort_url('created_at', $sort, $order) ?>" class="sortable text-white">
                                Created At
                                <?php if ($sort === 'created_at'): ?>
                                    <span class="sort-icon"><?= $order === 'ASC' ? '▲' : '▼' ?></span>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $method_display = 'Unknown';
                        if ($row['creation_method'] === 'Normal') $method_display = 'Normal Sign Up';
                        elseif ($row['creation_method'] === 'Custom') $method_display = 'Created By an Admin';
                    ?>
                        <tr>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td>
                                <i class="fas fa-phone-alt me-1"></i><?= htmlspecialchars($row['mobile']) ?><br>
                                <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($row['email']) ?><br>
                                <i class="fas fa-id-card me-1"></i><?= htmlspecialchars($row['nic']) ?>
                            </td>
                            <td>
                                <strong>District:</strong> <?= htmlspecialchars($row['district']) ?><br>
                                <strong>Province:</strong> <?= htmlspecialchars($row['province']) ?><br>
                                <strong>Address:</strong> <?= htmlspecialchars($row['address']) ?>
                            </td>
                            <td><?= htmlspecialchars($row['user_type']) ?></td>
                            <td><?= htmlspecialchars($method_display) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="includes/edit_user_password_by_admin.php?id=<?= $row['user_id'] ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>&user_type=<?= urlencode($user_type_filter) ?>&creation_method=<?= urlencode($creation_method_filter) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?>" class="btn btn-warning btn-sm me-1" title="Reset Password" onclick="return confirm('Are you sure you want to reset this user\'s password to Temppass123?');"><i class="fas fa-key"></i> Reset Password</a>
                                <a href="delete_user.php?id=<?= $row['user_id'] ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>&user_type=<?= urlencode($user_type_filter) ?>&creation_method=<?= urlencode($creation_method_filter) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?>" class="btn btn-danger btn-sm me-1" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                <button type="button" class="btn btn-info btn-sm me-1 see-more-btn" 
                                    data-userid="<?= $row['user_id'] ?>" 
                                    data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-fullname="<?= htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-mobile="<?= htmlspecialchars($row['mobile'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-nic="<?= htmlspecialchars($row['nic'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-district="<?= htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-province="<?= htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-address="<?= htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-usertype="<?= htmlspecialchars($row['user_type'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-method_display="<?= htmlspecialchars($method_display, ENT_QUOTES, 'UTF-8') ?>" 
                                    data-created="<?= date('Y-m-d H:i', strtotime($row['created_at'])) ?>" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#userDetailModal" 
                                    title="See More">
                                    <i class="fas fa-eye"></i> See More
                                </button>
                                <button type="button" class="btn btn-primary btn-sm edit-user-btn"
                                    data-userid="<?= $row['user_id'] ?>"
                                    data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-fullname="<?= htmlspecialchars($row['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-mobile="<?= htmlspecialchars($row['mobile'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-nic="<?= htmlspecialchars($row['nic'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-district="<?= htmlspecialchars($row['district'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-province="<?= htmlspecialchars($row['province'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-address="<?= htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-usertype="<?= htmlspecialchars($row['user_type'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"
                                    title="Edit User">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>
                                <!-- ✅ New Audit Log Button -->
                                <button type="button" class="btn btn-secondary btn-sm audit-log-btn"
                                    data-userid="<?= $row['user_id'] ?>"
                                    data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#auditLogModal"
                                    title="View Audit Log">
                                    <i class="fas fa-history"></i> Audit Log
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- First page -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= build_page_url(1) ?>" title="First">&laquo;</a>
                    </li>
                    <!-- Previous page -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= build_page_url($page - 1) ?>" title="Previous">&lsaquo;</a>
                    </li>
                    <!-- Page X of Y -->
                    <li class="page-item disabled">
                        <span class="page-link">Page <?= $page ?> of <?= $total_pages ?></span>
                    </li>
                    <!-- Next page -->
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= build_page_url($page + 1) ?>" title="Next">&rsaquo;</a>
                    </li>
                    <!-- Last page -->
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= build_page_url($total_pages) ?>" title="Last">&raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">No users found.</div>
    <?php endif; ?>
</div>

<!-- User Detail Modal (See More) -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="userDetailModalLabel">User Full Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="userDetailModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="Includes/edit_user_by_admin.php" method="POST" id="editUserForm">
        <div class="modal-body">
          <?php if ($edit_modal_open && !empty($edit_errors)): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($edit_errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <input type="hidden" name="user_id" id="edit_user_id" value="<?= $edit_modal_open ? $edit_user_id : '' ?>">
          <input type="hidden" name="page" value="<?= $page ?>">
          <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
          <input type="hidden" name="user_type_filter" value="<?= htmlspecialchars($user_type_filter) ?>">
          <input type="hidden" name="creation_method" value="<?= htmlspecialchars($creation_method_filter) ?>">
          <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
          <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_username" class="form-label">Username *</label>
              <input type="text" class="form-control" id="edit_username" name="username" value="<?= edit_value('username', $edit_old_input, $edit_user_original) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_fullname" class="form-label">Full Name *</label>
              <input type="text" class="form-control" id="edit_fullname" name="fullname" value="<?= edit_value('fullname', $edit_old_input, $edit_user_original) ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_mobile" class="form-label">Mobile *</label>
              <input type="text" class="form-control" id="edit_mobile" name="mobile" value="<?= edit_value('mobile', $edit_old_input, $edit_user_original) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="edit_email" name="email" value="<?= edit_value('email', $edit_old_input, $edit_user_original) ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_nic" class="form-label">NIC *</label>
              <input type="text" class="form-control" id="edit_nic" name="nic" value="<?= edit_value('nic', $edit_old_input, $edit_user_original) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_user_type" class="form-label">User Type *</label>
              <select class="form-control" id="edit_user_type" name="user_type" required>
                <option value="Parents" <?= edit_value('user_type', $edit_old_input, $edit_user_original) == 'Parents' ? 'selected' : '' ?>>Parents</option>
                <option value="Vehicle Owner" <?= edit_value('user_type', $edit_old_input, $edit_user_original) == 'Vehicle Owner' ? 'selected' : '' ?>>Vehicle Owner</option>
                <option value="admin" <?= edit_value('user_type', $edit_old_input, $edit_user_original) == 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="edit_province" class="form-label">Province *</label>
              <select class="form-control" id="edit_province" name="province" required>
                <option value="">-- Select Province --</option>
                <?php foreach ($province_district_map as $prov => $districts): 
                    $selected = (edit_value('province', $edit_old_input, $edit_user_original) == $prov) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($prov) ?>" <?= $selected ?>><?= htmlspecialchars($prov) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_district" class="form-label">District *</label>
              <select class="form-control" id="edit_district" name="district" required>
                <option value="">-- Select District --</option>
                <!-- Options will be populated via JavaScript -->
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <label for="edit_address" class="form-label">Address *</label>
              <textarea class="form-control" id="edit_address" name="address" rows="2" required><?= edit_value('address', $edit_old_input, $edit_user_original) ?></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ Audit Log Modal -->
<div class="modal fade" id="auditLogModal" tabindex="-1" aria-labelledby="auditLogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="auditLogModalLabel">Audit Log - <span id="audit-username"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="auditLogModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading audit logs...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Sri Lanka province-district mapping
const provinceDistricts = <?= json_encode($province_district_map) ?>;

function updateDistrictDropdown(provinceSelect, districtSelect, selectedDistrict = null) {
    const province = provinceSelect.value;
    districtSelect.innerHTML = '<option value="">-- Select District --</option>';
    if (province && provinceDistricts[province]) {
        provinceDistricts[province].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            if (selectedDistrict && district === selectedDistrict) {
                option.selected = true;
            }
            districtSelect.appendChild(option);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // See More modal
    document.querySelectorAll('.see-more-btn').forEach(button => {
        button.addEventListener('click', function () {
            const modalBody = document.getElementById('userDetailModalBody');
            const data = this.dataset;
            let html = '<table class="table table-bordered table-striped">';
            const fields = [
                ['User ID', data.userid], ['Username', data.username], ['Full Name', data.fullname],
                ['Mobile', data.mobile], ['Email', data.email], ['NIC', data.nic],
                ['District', data.district], ['Province', data.province], ['Address', data.address],
                ['User Type', data.usertype], ['Creation Method', data.method_display], ['Created At', data.created]
            ];
            fields.forEach(([label, value]) => {
                html += `<tr><th style="width:200px;">${label}</th><td>${value}</td></tr>`;
            });
            html += '</table>';
            modalBody.innerHTML = html;
        });
    });

    // Edit User modal
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function () {
            const data = this.dataset;
            document.getElementById('edit_user_id').value = data.userid;
            document.getElementById('edit_username').value = data.username;
            document.getElementById('edit_fullname').value = data.fullname;
            document.getElementById('edit_mobile').value = data.mobile;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_nic').value = data.nic;
            document.getElementById('edit_user_type').value = data.usertype;
            document.getElementById('edit_address').value = data.address;

            const provinceSelect = document.getElementById('edit_province');
            const districtSelect = document.getElementById('edit_district');
            provinceSelect.value = data.province || '';
            updateDistrictDropdown(provinceSelect, districtSelect, data.district);
        });
    });

    // ✅ Audit Log Modal
    document.querySelectorAll('.audit-log-btn').forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userid;
            const username = this.dataset.username;
            document.getElementById('audit-username').textContent = username + ' (ID: ' + userId + ')';
            const modalBody = document.getElementById('auditLogModalBody');
            // Show loading
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p>Loading audit logs...</p></div>';
            fetch('get_user_audit_log.php?user_id=' + userId)
              .then(response => response.text())
              .then(html => {
                  modalBody.innerHTML = html;
              })
              .catch(err => {
                  modalBody.innerHTML = '<div class="alert alert-danger">Failed to load audit logs.</div>';
              });
        });
    });

    // Clear audit modal content on close
    document.getElementById('auditLogModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('auditLogModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p>Loading audit logs...</p></div>';
    });

    // Province-District change
    const provinceSelect = document.getElementById('edit_province');
    const districtSelect = document.getElementById('edit_district');
    provinceSelect.addEventListener('change', function () {
        updateDistrictDropdown(provinceSelect, districtSelect, null);
    });

    <?php if ($edit_modal_open): ?>
        var editModalEl = document.getElementById('editUserModal');
        var editModal = new bootstrap.Modal(editModalEl);
        editModal.show();

        const savedProvince = document.getElementById('edit_province').value;
        const savedDistrict = "<?= edit_value('district', $edit_old_input, $edit_user_original) ?>";
        updateDistrictDropdown(provinceSelect, districtSelect, savedDistrict);
    <?php endif; ?>

    editModalEl.addEventListener('hidden.bs.modal', function () {
        var params = new URLSearchParams(window.location.search);
        params.delete('edit_modal');
        params.delete('edit_user_id');
        window.location.href = 'user_management.php?' + params.toString();
    });
});
</script>

</body>
</html>