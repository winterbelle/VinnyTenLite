<?php
session_start();

// Only admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "VTR");

// ---- Filters / Search / Pagination ----
$q      = isset($_GET['q']) ? trim($_GET['q']) : "";
$status = isset($_GET['status']) ? $_GET['status'] : "all";  // all | unread
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";     // "" | today

$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$whereParts = [];
$whereParts[] = "1=1";

if ($status === "unread") {
    $whereParts[] = "is_read = 0";
}
if ($filter === "today") {
    $whereParts[] = "DATE(created_at) = CURDATE()";
}
if ($q !== "") {
    $safeQ = $conn->real_escape_string('%' . $q . '%');
    $whereParts[] = "(name LIKE '$safeQ' OR email LIKE '$safeQ' OR message LIKE '$safeQ')";
}

$whereSql = "WHERE " . implode(" AND ", $whereParts);

// ---- Actions (mark read, delete, export) ----

// Mark as read
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $conn->query("UPDATE feedback SET is_read = 1 WHERE id = $id");
    header("Location: admin_feedback.php?marked=1&" . http_build_query([
        'q' => $q,
        'status' => $status,
        'filter' => $filter,
        'page' => $page
    ]));
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM feedback WHERE id = $id");
    header("Location: admin_feedback.php?deleted=1&" . http_build_query([
        'q' => $q,
        'status' => $status,
        'filter' => $filter,
        'page' => $page
    ]));
    exit;
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=feedback_export.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Message', 'Read', 'Created At']);

    $exportResult = $conn->query("SELECT * FROM feedback $whereSql ORDER BY created_at DESC");
    while ($row = $exportResult->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['email'],
            $row['message'],
            $row['is_read'] ? 'Yes' : 'No',
            $row['created_at']
        ]);
    }
    fclose($output);
    exit;
}

// Count for pagination
$countRes = $conn->query("SELECT COUNT(*) AS total FROM feedback $whereSql");
$totalRows = $countRes->fetch_assoc()['total'] ?? 0;
$totalPages = max(1, ceil($totalRows / $limit));

// Fetch rows
$result = $conn->query("SELECT * FROM feedback $whereSql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

// Flash messages
$flash = "";
if (isset($_GET['deleted'])) {
    $flash = "Message deleted successfully.";
} elseif (isset($_GET['marked'])) {
    $flash = "Message marked as read.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Messages - Admin</title>

    <link rel="stylesheet" href="styles/global.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="styles/dropdown.css">

    <style>
        .feedback-wrapper {
            width: 90%;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #013783;
        }

        .feedback-wrapper h2 {
            font-size: 32px;
            color: #013783;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .filters-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 15px;
        }

        .filters-bar form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filters-bar input[type="text"],
        .filters-bar select {
            padding: 5px 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filters-bar button,
        .filters-bar a.export-btn {
            padding: 6px 10px;
            border-radius: 5px;
            border: none;
            background: #013783;
            color: white;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .filters-bar button:hover,
        .filters-bar a.export-btn:hover {
            background: #d50000;
        }

        .flash {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #d4f4d4;
            color: #006600;
            font-weight: bold;
            animation: fadeOut 2.2s ease forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #f8f8f8;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        th {
            background: #013783;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }

        .badge-unread {
            display: inline-block;
            padding: 3px 7px;
            background: #d50000;
            color: white;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-read {
            display: inline-block;
            padding: 3px 7px;
            background: #00a000;
            color: white;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .action-btn {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            text-decoration: none;
            color: white;
            margin: 2px 0;
        }

        .view-btn { background: #013783; }
        .read-btn { background: #00a000; }
        .delete-btn { background: #d50000; }

        .action-btn:hover {
            opacity: 0.85;
        }

        .pagination {
            margin-top: 15px;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            margin: 0 3px;
            padding: 5px 9px;
            border-radius: 5px;
            border: 1px solid #013783;
            text-decoration: none;
            color: #013783;
            font-size: 14px;
        }

        .pagination .current {
            background: #013783;
            color: white;
            font-weight: bold;
        }

        /* Modal styling */
        #modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        #modal-box {
            background: white;
            max-width: 600px;
            width: 90%;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #013783;
            max-height: 80vh;
            overflow-y: auto;
        }

        #modal-box h3 {
            margin-top: 0;
            color: #013783;
        }

        #modal-close {
            float: right;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            color: #d50000;
        }

        .modal-meta {
            font-size: 13px;
            color: #555;
            margin-bottom: 10px;
        }

        .modal-message {
            white-space: pre-wrap;
            font-size: 14px;
        }
    </style>

    <script src="scripts/adminFeedback.js" defer></script>

</head>

<body>

<?php include "header.php"; ?>

<div class="feedback-wrapper">
    <h2>📨 Feedback Messages</h2>

    <?php if (!empty($flash)): ?>
        <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="filters-bar">
        <form method="get" action="admin_feedback.php">
            <input type="text" name="q" placeholder="Search name/email/message"
                   value="<?= htmlspecialchars($q) ?>">

            <select name="status">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                <option value="unread" <?= $status === 'unread' ? 'selected' : '' ?>>Unread Only</option>
            </select>

            <select name="filter">
                <option value="" <?= $filter === '' ? 'selected' : '' ?>>Any Date</option>
                <option value="today" <?= $filter === 'today' ? 'selected' : '' ?>>Today</option>
            </select>

            <button type="submit">Apply</button>
        </form>

        <a class="export-btn" href="admin_feedback.php?<?= http_build_query([
            'q' => $q,
            'status' => $status,
            'filter' => $filter,
            'export' => 'csv'
        ]) ?>">⬇ Export CSV</a>
    </div>

    <table>
        <tr>
            <th>Status</th>
            <th>Name / Email</th>
            <th>Message (Preview)</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php if ($totalRows == 0): ?>
            <tr>
                <td colspan="5" style="text-align:center;">No feedback found.</td>
            </tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $id        = $row['id'];
                    $name      = $row['name'];
                    $email     = $row['email'];
                    $msg       = $row['message'];
                    $createdAt = $row['created_at'];
                    $isRead    = (int)$row['is_read'] === 1;

                    $preview = strlen($msg) > 80 ? substr($msg, 0, 80) . '...' : $msg;
                ?>
                <tr>
                    <td>
                        <?php if ($isRead): ?>
                            <span class="badge-read">Read</span>
                        <?php else: ?>
                            <span class="badge-unread">Unread</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <strong id="fb-name-<?= $id ?>"><?= htmlspecialchars($name) ?></strong><br>
                        <span id="fb-email-<?= $id ?>"><?= htmlspecialchars($email) ?></span>
                    </td>

                    <td>
                        <span id="fb-msg-<?= $id ?>" style="display:none;"><?= htmlspecialchars($msg) ?></span>
                        <?= htmlspecialchars($preview) ?>
                    </td>

                    <td id="fb-date-<?= $id ?>">
                        <?= htmlspecialchars($createdAt) ?>
                    </td>

                    <td>
                        <a href="javascript:void(0)" class="action-btn view-btn" onclick="openModal(<?= $id ?>)">View</a>
                        <?php if (!$isRead): ?>
                            <a href="javascript:void(0)" class="action-btn read-btn" onclick="markRead(<?= $id ?>)">Mark Read</a>
                        <?php endif; ?>
                        <a href="javascript:void(0)" class="action-btn delete-btn" onclick="confirmDelete(<?= $id ?>)">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $page): ?>
                    <span class="current"><?= $p ?></span>
                <?php else: ?>
                    <a href="admin_feedback.php?<?= http_build_query([
                        'q' => $q,
                        'status' => $status,
                        'filter' => $filter,
                        'page' => $p
                    ]) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Modal -->
<div id="modal-overlay" onclick="closeModal()">
    <div id="modal-box" onclick="event.stopPropagation();">
        <span id="modal-close" onclick="closeModal()">✖</span>
        <h3>Feedback Message</h3>
        <div class="modal-meta">
            <strong id="modal-name"></strong> • <span id="modal-email"></span><br>
            <span id="modal-date"></span>
        </div>
        <div class="modal-message" id="modal-message"></div>
    </div>
</div>

<?php include "footer.php"; ?>

</body>
</html>
