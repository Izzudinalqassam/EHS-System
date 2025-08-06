<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Include file koneksi database
include "koneksi.php";

// Pagination settings
$records_per_page = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filtering
$filter_username = isset($_GET['username']) ? $_GET['username'] : '';
$filter_role = isset($_GET['role']) ? $_GET['role'] : '';
$filter_activity = isset($_GET['activity']) ? $_GET['activity'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// Base query
$query = "SELECT * FROM activity_logs WHERE 1=1";
$count_query = "SELECT COUNT(*) as total FROM activity_logs WHERE 1=1";

// Apply filters
if (!empty($filter_username)) {
    $filter_username = mysqli_real_escape_string($konek, $filter_username);
    $query .= " AND username LIKE '%$filter_username%'";
    $count_query .= " AND username LIKE '%$filter_username%'";
}

if (!empty($filter_role)) {
    $filter_role = mysqli_real_escape_string($konek, $filter_role);
    $query .= " AND role = '$filter_role'";
    $count_query .= " AND role = '$filter_role'";
}

if (!empty($filter_activity)) {
    $filter_activity = mysqli_real_escape_string($konek, $filter_activity);
    $query .= " AND activity LIKE '%$filter_activity%'";
    $count_query .= " AND activity LIKE '%$filter_activity%'";
}

if (!empty($filter_date)) {
    $filter_date = mysqli_real_escape_string($konek, $filter_date);
    $query .= " AND DATE(created_at) = '$filter_date'";
    $count_query .= " AND DATE(created_at) = '$filter_date'";
}

// Order and limit
$query .= " ORDER BY created_at DESC LIMIT $offset, $records_per_page";

// Pastikan tabel activity_logs ada, jika tidak maka buat
$check_table = mysqli_query($konek, "SHOW TABLES LIKE 'activity_logs'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE `activity_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `role` enum('admin','security') NOT NULL,
        `activity` varchar(255) NOT NULL,
        `ip_address` varchar(50) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if(!mysqli_query($konek, $create_table)) {
        die("Error creating table: " . mysqli_error($konek));
    }
    
    // Tambahkan beberapa data sample
    $sample_data = [
        ["admin", "admin", "Logged in to the system", "127.0.0.1"],
        ["security", "security", "Scanned RFID for employee entry", "127.0.0.1"],
        ["admin", "admin", "Updated employee data", "127.0.0.1"],
        ["security", "security", "Registered visitor entry", "127.0.0.1"]
    ];
    
    foreach ($sample_data as $data) {
        mysqli_query($konek, "INSERT INTO activity_logs (username, role, activity, ip_address) VALUES ('$data[0]', '$data[1]', '$data[2]', '$data[3]')");
    }
}

// Execute queries
$result = mysqli_query($konek, $query);
$count_result = mysqli_query($konek, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Activity Logs</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="image/bp.png" type="image/x-icon">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    
    <style>
        .log-container {
            background-color: #ffffff; 
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .log-header {
            position: relative;
            margin: 20px auto 40px;
            padding: 25px 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 46%, #D946EF 100%);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            color: white;
            overflow: hidden;
            text-align: left;
        }
        
        .log-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .badge-admin {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .badge-security {
            background-color: #ff9800;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-item {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include 'components/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'components/sidenav.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <div class="log-header animate-item">
                        <h2><i class="fas fa-history mr-2"></i> Activity Logs</h2>
                        <span class="role-badge">Admin Access</span>
                    </div>
                    
                    <!-- Filter Section -->
                    <div class="filter-section animate-item">
                        <form method="GET" action="" class="row">
                            <div class="col-md-2 mb-2">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($filter_username) ?>">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="role">Role</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="">All Roles</option>
                                    <option value="admin" <?= $filter_role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="security" <?= $filter_role === 'security' ? 'selected' : '' ?>>Security</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="activity">Activity</label>
                                <input type="text" class="form-control" id="activity" name="activity" value="<?= htmlspecialchars($filter_activity) ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="date">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary flex-grow-1 mr-2">Filter</button>
                                    <a href="activity_logs.php" class="btn btn-secondary flex-grow-1">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Logs Table -->
                    <div class="log-container animate-item">
                        <div class="table-responsive">
                            <table class="table table-bordered log-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Activity</th>
                                        <th>IP Address</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td>
                                                    <?php if ($row['role'] === 'admin'): ?>
                                                        <span class="badge-admin">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge-security">Security</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['activity']) ?></td>
                                                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                                                <td><?= date('d M Y H:i:s', strtotime($row['created_at'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No logs found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page-1 ?>&username=<?= urlencode($filter_username) ?>&role=<?= urlencode($filter_role) ?>&activity=<?= urlencode($filter_activity) ?>&date=<?= urlencode($filter_date) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&username=<?= urlencode($filter_username) ?>&role=<?= urlencode($filter_role) ?>&activity=<?= urlencode($filter_activity) ?>&date=<?= urlencode($filter_date) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page+1 ?>&username=<?= urlencode($filter_username) ?>&role=<?= urlencode($filter_role) ?>&activity=<?= urlencode($filter_activity) ?>&date=<?= urlencode($filter_date) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
                        <!-- Stats Section -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Logs</h5>
                                        <h1 class="display-4"><?= $total_records ?></h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Admin Actions</h5>
                                        <?php
                                        $admin_count_query = "SELECT COUNT(*) as total FROM activity_logs WHERE role = 'admin'";
                                        $admin_count_result = mysqli_query($konek, $admin_count_query);
                                        $admin_count = mysqli_fetch_assoc($admin_count_result)['total'];
                                        ?>
                                        <h1 class="display-4"><?= $admin_count ?></h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Security Actions</h5>
                                        <?php
                                        $security_count_query = "SELECT COUNT(*) as total FROM activity_logs WHERE role = 'security'";
                                        $security_count_result = mysqli_query($konek, $security_count_query);
                                        $security_count = mysqli_fetch_assoc($security_count_result)['total'];
                                        ?>
                                        <h1 class="display-4"><?= $security_count ?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php include 'components/footer.php'; ?>
        </div>
    </div>

    <script src="js/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements
            const items = document.querySelectorAll('.animate-item');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.animation = 'fadeIn 0.6s ease-out forwards';
                }, 100 * (index + 1));
            });
        });
    </script>
</body>
</html>