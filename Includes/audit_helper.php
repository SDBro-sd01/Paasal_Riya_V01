<?php
/**
 * Audit Log Helper
 * user activity track කිරීම සඳහා
 */

/**
 * Sanitize data array for audit - remove password or mask it
 */
function sanitizeForAudit(array $data): array {
    // Password field ඉවත් කරන්න හෝ mask කරන්න
    if (isset($data['password'])) {
        $data['password'] = '********';
    }
    // User creation method table එකේ data audit නොකරන්න (optional)
    return $data;
}

/**
 * Insert an audit record
 *
 * @param mysqli  $conn       Database connection
 * @param string  $table      Table name (e.g., 'users')
 * @param int     $record_id  Affected row ID
 * @param string  $action     'INSERT', 'UPDATE', or 'DELETE'
 * @param array|null $old_data  Old values as associative array (null for INSERT)
 * @param array|null $new_data  New values as associative array
 * @param int|null $changed_by User ID who performed action (null for system/public)
 */
function insertAuditLog(
    mysqli $conn,
    string $table,
    int $record_id,
    string $action,
    ?array $old_data,
    ?array $new_data,
    ?int $changed_by = null
): void {
    // Allowed actions validate
    $allowed_actions = ['INSERT', 'UPDATE', 'DELETE'];
    if (!in_array($action, $allowed_actions)) {
        return;
    }

    // Sanitize data
    if ($old_data !== null) {
        $old_data = sanitizeForAudit($old_data);
    }
    if ($new_data !== null) {
        $new_data = sanitizeForAudit($new_data);
    }

    // Encode as JSON
    $old_json = $old_data ? json_encode($old_data, JSON_UNESCAPED_UNICODE) : null;
    $new_json = $new_data ? json_encode($new_data, JSON_UNESCAPED_UNICODE) : null;

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $conn->prepare(
        "INSERT INTO audit_log (table_name, record_id, action, old_values, new_values, changed_by, ip_address)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sisssis", $table, $record_id, $action, $old_json, $new_json, $changed_by, $ip);
    $stmt->execute();
    $stmt->close();
}