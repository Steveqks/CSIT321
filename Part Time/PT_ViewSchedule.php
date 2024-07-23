<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

$user_id = $_SESSION['UserID'];
$FirstName = $_SESSION['FirstName'];
$companyID = $_SESSION['CompanyID'];
$role = $_SESSION['Role'];

// Connect to the database
$conn = OpenCon();

$limit = 10; // Number of entries to show in a page.
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$start_from = ($page - 1) * $limit;

// Fetch schedule for the user starting from today
$sql = "SELECT WorkDate, StartWork, EndWork
        FROM schedule
        WHERE UserID = ? AND WorkDate >= CURDATE()
        ORDER BY WorkDate ASC
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $start_from, $limit);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_all(MYSQLI_ASSOC);

// Get the total number of records
$sql_total = "SELECT COUNT(*) FROM schedule WHERE UserID = ? AND WorkDate >= CURDATE()";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$stmt_total->bind_result($total_records);
$stmt_total->fetch();

$total_pages = ceil($total_records / $limit);

$stmt->close();
$stmt_total->close();
CloseCon($conn);

// Function to calculate pagination range
function getPaginationRange($current_page, $total_pages, $display_range = 10)
{
    $start = max(1, $current_page - intval($display_range / 2));
    $end = min($total_pages, $start + $display_range - 1);

    if ($end - $start < $display_range - 1) {
        $start = max(1, $end - $display_range + 1);
    }

    return range($start, $end);
}

$pagination_range = getPaginationRange($page, $total_pages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule - Part-Time Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .top-section {
            border: 1px solid black;
            height: 20vh;
            overflow: hidden;
            text-align: left;
            padding: 10px;
        }

        .top-section img {
            height: 100%;
            width: auto;
        }

        .middle-section {
            display: flex;
            border: 1px solid black;
            height: 95vh;
        }

        .navbar {
            border: 1px solid black;
            width: 200px;
            padding: 0;
            background-color: #f8f8f8;
            box-sizing: border-box;
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            width: 200px;
        }

        .navbar li {
            margin: 0;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            display: block;
            width: calc(100% - 1px);
            padding: 10px;
            border: 0.5px solid black;
            transition: background-color 0.3s, color 0.3s;
            box-sizing: border-box;
            border-width: 1px 0px 0px 0px;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: #000;
            border: 0.5px solid black;
        }

        .content-section {
            padding: 20px;
            flex-grow: 1;
        }

        .header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            display: inline-block;
        }
		
		.header i {
            margin-right: 10px;
        }

        .view-past-schedule-link {
            margin-top: 10px;
            margin-bottom: 20px;
            display: block;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }

        .schedule-table th, .schedule-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .schedule-table th {
            background-color: #f2f2f2;
        }

        .schedule-table td {
            white-space: nowrap;
        }

        .view-past-schedule-link a {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .view-past-schedule-link a:hover {
            background-color: #0056b3;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        .pagination li {
            display: inline;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            text-decoration: none;
            color: #007BFF;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active a {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }

        .pagination .disabled a {
            color: #ccc;
            pointer-events: none;
            cursor: default;
        }
    </style>
</head>
<body>
    <!-- TOP SECTION -->
    <div class="top-section">
        <img src="Images/tms.png" alt="TrackMySchedule Logo">
    </div>
    
    <!-- MIDDLE SECTION -->
    <div class="middle-section">
        <!-- LEFT SECTION (NAVIGATION BAR) -->
        <?php include 'navbar.php'; ?>
        
        <!-- RIGHT SECTION (SCHEDULE) -->
        <div class="content-section">
            <div class="header">
                <i class="fas fa-calendar-alt"></i>
                <h2>View Schedule</h2>
            </div>
            <div class="view-past-schedule-link">
                <a href="PT_ViewPastSchedule.php">View Past Schedule</a>
            </div>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Work Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($schedule) > 0): ?>
                        <?php foreach ($schedule as $shift): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($shift['WorkDate']); ?></td>
                                <td><?php echo htmlspecialchars($shift['StartWork']); ?></td>
                                <td><?php echo htmlspecialchars($shift['EndWork']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No upcoming schedules found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="PT_ViewSchedule.php?page=<?php echo $page - 1; ?>">&laquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&laquo;</a></li>
                <?php endif; ?>

                <?php foreach ($pagination_range as $p): ?>
                    <?php if ($p == $page): ?>
                        <li class="active"><a href="#"><?php echo $p; ?></a></li>
                    <?php else: ?>
                        <li><a href="PT_ViewSchedule.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($page < $total_pages): ?>
                    <li><a href="PT_ViewSchedule.php?page=<?php echo $page + 1; ?>">&raquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&raquo;</a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
