<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

$user_id = $_SESSION['UserID'];
$Email = $_SESSION['Email'];
$FirstName = $_SESSION['FirstName'];

// Connect to the database
$conn = OpenCon();

// Pagination logic
$limit = 10; // Number of entries to show in a page
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$start_from = ($page - 1) * $limit;

// Fetch total number of attendance records
$sql_total = "SELECT COUNT(AttendanceID) FROM attendance WHERE UserID = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$stmt_total->bind_result($total_records);
$stmt_total->fetch();
$stmt_total->close();

$total_pages = ceil($total_records / $limit);

// Fetch attendance records for the current page
$sql = "SELECT DATE(ClockIn) AS Date, TIME(ClockIn) AS StartTime, TIME(ClockOut) AS EndTime, NumOfOverTimeHours 
        FROM attendance 
        WHERE UserID = ? 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $start_from, $limit);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_all(MYSQLI_ASSOC);

// Close the database connection
$stmt->close();
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
    <title>View Attendance (PT)</title>
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

        .attendance-section {
            padding: 20px;
            flex-grow: 1;
        }

        .attendance-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .attendance-header i {
            margin-right: 10px;
        }

        .attendance-header h2 {
            margin: 0;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .attendance-table th, .attendance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .attendance-table th {
            background-color: #cccccc;
            color: black;
        }

        .status {
            color: green;
            font-weight: bold;
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
        
        <!-- RIGHT SECTION (ATTENDANCE TABLE) -->
        <div class="attendance-section">
            <div class="attendance-header">
                <i class="fas fa-calendar-check"></i>
                <h2>View Attendance</h2>
            </div>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Overtime Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($attendance) > 0): ?>
                        <?php foreach ($attendance as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['Date']); ?></td>
                                <td><?php echo htmlspecialchars($record['StartTime']); ?></td>
                                <td><?php echo htmlspecialchars($record['EndTime']); ?></td>
                                <td><?php echo htmlspecialchars($record['NumOfOverTimeHours']); ?></td>
                                <td class="status">Present</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1 && $total_records > 0): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="PT_ViewAttendance.php?page=<?php echo $page - 1; ?>">&laquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&laquo;</a></li>
                <?php endif; ?>

                <?php foreach ($pagination_range as $p): ?>
                    <?php if ($p == $page): ?>
                        <li class="active"><a href="#"><?php echo $p; ?></a></li>
                    <?php else: ?>
                        <li><a href="PT_ViewAttendance.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($page < $total_pages): ?>
                    <li><a href="PT_ViewAttendance.php?page=<?php echo $page + 1; ?>">&raquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&raquo;</a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
