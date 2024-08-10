
<?php
session_start();
include 'db_connection.php';

	// Check if user is logged in
	include '../Session/session_check_user_FT.php';
	
	$user_id = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$FirstName = $_SESSION['FirstName'];
			
	// Connect to the database
	$conn = OpenCon();

	// Pagination logic
	$limit = 10; // Number of entries to show in a page
	$page = isset($_GET["page"]) ? $_GET["page"] : 1;
	$start_from = ($page - 1) * $limit;

	// Fetch total number of tasks
	$sql_total = "SELECT COUNT(t.TaskID) 
				  FROM task t
				  JOIN taskinfo ti ON t.MainTaskID = ti.MainTaskID
				  WHERE t.UserID = $user_id";
	$stmt_total = $conn->prepare($sql_total);
	$stmt_total->execute();
	$stmt_total->bind_result($total_records);
	$stmt_total->fetch();
	$stmt_total->close();

	$total_pages = ceil($total_records / $limit);

	// Fetch tasks for the current page
	$sql = "SELECT t.TaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.TaskDesc 
			FROM task t
			JOIN taskinfo ti ON t.MainTaskID = ti.MainTaskID
			WHERE t.UserID = $user_id
			LIMIT ?, ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ii", $start_from, $limit);
	$stmt->execute();
	$result = $stmt->get_result();
	$tasks = $result->fetch_all(MYSQLI_ASSOC);

	// Close the database connection
	$stmt->close();
	CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - Tasks (PT)</title>
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

        .task-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .task-table th, .task-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .task-table th {
            background-color: #cccccc;
            color: black;
        }

        .task-section {
            padding: 20px;
            flex-grow: 1;
        }

        .task-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .task-header i {
            margin-right: 10px;
        }

        .task-header h2 {
            margin: 0;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 10px 0;
        }

        .pagination a {
            text-decoration: none;
            color: #333;
            padding: 8px 16px;
            border: 1px solid #ddd;
            margin: 0 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
            color: #000;
        }

        .pagination a.active {
            background-color: #333;
            color: white;
        }

        .pagination a.disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        /* Styles for collapsible content */
        .task-desc {
            display: none;
            padding: 10px;
            border: 1px solid #ddd;
            border-top: none;
            background-color: #f9f9f9;
        }

        .task-name {
            cursor: pointer;
            background-color: #f2f2f2;
            transition: background-color 0.3s;
        }

        .task-name:hover {
            background-color: #ddd;
        }

        .expand-icon {
            margin-right: 5px;
            font-size: 12px;
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
        <div class="navbar">
            <ul>
                <li><a href="FT_HomePage.php"><?php echo "$FirstName, Staff(FT)"?></a></li>
                <li><a href="FT_AccountDetails.php">Manage Account</a></li>
                <li><a href="FT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="FT_ViewNewsFeed.php">View News Feed</a></li>
				<li><a href="FT_ReviewManagement.php">Leave a Review!</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
       <!-- RIGHT SECTION (TASK TABLE) -->
        <div class="task-section">
            <div class="task-header">
                <i class="fas fa-tasks"></i>
                <h2>View Tasks</h2>
            </div>
            <table class="task-table">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr class="task-name" onclick="toggleDescription(<?php echo $task['TaskID']; ?>)">
                                <td><i class="fas fa-angle-down expand-icon" id="icon-<?php echo $task['TaskID']; ?>"></i><?php echo htmlspecialchars($task['TaskName']); ?></td>
                                <td><?php echo htmlspecialchars($task['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($task['DueDate']); ?></td>
                            </tr>
                            <tr id="desc-<?php echo $task['TaskID']; ?>" class="task-desc">
                                <td colspan="3"><?php echo htmlspecialchars($task['TaskDesc']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No tasks assigned.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination controls -->
            <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="FT_HomePage.php?page=<?php echo $page - 1; ?>">&laquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&laquo;</a></li>
                <?php endif; ?>

                <?php foreach ($pagination_range as $p): ?>
                    <?php if ($p == $page): ?>
                        <li class="active"><a href="#"><?php echo $p; ?></a></li>
                    <?php else: ?>
                        <li><a href="FT_HomePage.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($page < $total_pages): ?>
                    <li><a href="FT_HomePage.php?page=<?php echo $page + 1; ?>">&raquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&raquo;</a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleDescription(taskID) {
            var descRow = document.getElementById("desc-" + taskID);
            var icon = document.getElementById("icon-" + taskID);
            if (descRow.style.display === "none" || descRow.style.display === "") {
                descRow.style.display = "table-row";
                icon.classList.remove("fa-angle-down");
                icon.classList.add("fa-angle-up");
            } else {
                descRow.style.display = "none";
                icon.classList.remove("fa-angle-up");
                icon.classList.add("fa-angle-down");
            }
        }
    </script>
</body>
</html>
