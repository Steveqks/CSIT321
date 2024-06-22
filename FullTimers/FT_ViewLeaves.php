<?php
	session_start();
	include 'db_connection.php';

	// Check if user is logged in
	if (!isset($_SESSION['Email'])) 
	{
		header("Location: ../Unregistered Users/LoginPage.php");
		exit();
	}

	$user_id = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$FirstName = $_SESSION['FirstName'];
			
	// Connect to the database
	$conn = OpenCon();

	// Fetch Leaves(Start Date, End Date, Leave Type, Half Day, Comments)
	$sql = "SELECT StartDate,EndDate,LeaveType,HalfDay,Comments FROM leaves WHERE UserID = $user_id";
	
	$stmt = $conn->prepare($sql);
	// $stmt->bind_param("i", $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$leaves = $result->fetch_all(MYSQLI_ASSOC);

	// Close the database connection
	$stmt->close();
	CloseCon($conn);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Leaves(FT)</title>
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
			height: 80vh;
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

		.view-leaves-table {
			width: 100%;
			border-collapse: collapse;
		}

		.view-leaves-table th, .view-leaves-table td {
			border: 1px solid #ddd;
			padding: 8px;
			text-align: left;
		}

		.view-leaves-table th {
			background-color: #f2f2f2;
		}

		.view-leaves-section {
			padding: 20px;
			flex-grow: 1;
		}

		.view-leaves-header {
			display: inline-flex;
			align-items: center;
			border-bottom: 1px solid black;
			padding-bottom: 5px;
			margin-bottom: 20px;
		}

		.view-leaves-header i {
			margin-right: 10px;
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
                <li><a href="#">Time Management</a></li>
                <li><a href="#">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (TASK TABLE) -->
        <div class="view-leaves-section">
            <div class="view-leaves-header">
                <i class="fas fa-user"></i>
                <h2>View My Leaves</h2>
            </div>
			
			<table class="view-leaves-table">
                <thead>
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Leave Type</th>
						<th>Half Day</th>
						<th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($leaves) > 0): ?>
                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($leave['EndDate']); ?></td>
                                <td><?php echo htmlspecialchars($leave['LeaveType']); ?></td>
								<td><?php echo htmlspecialchars($leave['HalfDay']); ?></td>
								<td><?php echo htmlspecialchars($leave['Comments']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No Leaves Applied.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
		
		
    </div>
</body>

</html>