<?php
session_start();
include "Includes/db_connection.php";

// Only allow logged-in admin (optional)
// if (!isset($_SESSION['admin_logged'])) { ... }

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($user_id <= 0) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit;
}

// Fetch audit logs for this user from audit_log table
$stmt = $conn->prepare("SELECT * FROM audit_log WHERE table_name = 'users' AND record_id = ? ORDER BY changed_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div class="alert alert-info">No audit logs found for this user.</div>';
    exit;
}
?>
<div class="table-responsive">
  <table class="table table-bordered table-striped table-sm">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Action</th>
        <th>Old Values</th>
        <th>New Values</th>
        <th>Changed By</th>
        <th>IP Address</th>
        <th>Timestamp</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($log = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $log['id'] ?></td>
          <td>
            <?php if ($log['action'] == 'INSERT'): ?>
              <span class="badge bg-success">INSERT</span>
            <?php elseif ($log['action'] == 'UPDATE'): ?>
              <span class="badge bg-warning text-dark">UPDATE</span>
            <?php elseif ($log['action'] == 'DELETE'): ?>
              <span class="badge bg-danger">DELETE</span>
            <?php else: ?>
              <?= htmlspecialchars($log['action']) ?>
            <?php endif; ?>
          </td>
          <td style="max-width:250px; word-break:break-all;">
            <?php if ($log['old_values']): ?>
              <pre style="white-space: pre-wrap; margin:0; font-size:0.8em;"><?php
                $old = json_decode($log['old_values'], true);
                if ($old) {
                  foreach ($old as $key => $value) {
                    echo htmlspecialchars("$key: $value") . "\n";
                  }
                } else {
                  echo htmlspecialchars($log['old_values']);
                }
              ?></pre>
            <?php else: ?>
              <em>N/A</em>
            <?php endif; ?>
          </td>
          <td style="max-width:250px; word-break:break-all;">
            <?php if ($log['new_values']): ?>
              <pre style="white-space: pre-wrap; margin:0; font-size:0.8em;"><?php
                $new = json_decode($log['new_values'], true);
                if ($new) {
                  foreach ($new as $key => $value) {
                    echo htmlspecialchars("$key: $value") . "\n";
                  }
                } else {
                  echo htmlspecialchars($log['new_values']);
                }
              ?></pre>
            <?php else: ?>
              <em>N/A</em>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($log['changed_by']): ?>
              <?php
                // Optionally fetch username of changed_by
                $userStmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
                $userStmt->bind_param("i", $log['changed_by']);
                $userStmt->execute();
                $userStmt->bind_result($changed_username);
                if ($userStmt->fetch()) {
                  echo htmlspecialchars($changed_username) . " (ID: {$log['changed_by']})";
                } else {
                  echo "ID: " . $log['changed_by'];
                }
                $userStmt->close();
              ?>
            <?php else: ?>
              System / Public
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></td>
          <td><?= date('Y-m-d H:i:s', strtotime($log['changed_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php
$stmt->close();
$conn->close();
?>