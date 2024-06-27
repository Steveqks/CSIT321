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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Details (PT)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
		body 
		{
			margin: 0;
			font-family: Arial, sans-serif;
		}
		.top-section 
		{
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

		.leave-section 
		{
			padding: 20px;
			flex-grow: 1;
		}

		.leave-header 
		{
			display: inline-flex;
			align-items: center;
			border-bottom: 1px solid black;
			padding-bottom: 5px;
			margin-bottom: 20px;
		}

		.leave-header i 
		{
			margin-right: 10px;
		}
		
		.leave-header h2 {
            margin: 0;
        }

        .leave-buttons {
            display: flex;
            flex-direction: column;
        }
		
		.leave-button {
            display: inline-block;
            width: 200px; /* Adjust the width to make buttons shorter */
            padding: 15px 20px;
            margin-bottom: 10px;
            background-color: #d3d3d3;
            color: black;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s, color 0.3s;
            border: none;
            border-radius: 0;
        }
		
		.leave-button:hover {
            background-color: #bbb;
            color: black;
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
                <li><a href="#"><?php echo "$FirstName, Staff(FT)"?></a></li>
                <li><a href="#">Manage Account</a></li>
                <li><a href="#">Attendance Management</a></li>
                <li><a href="FT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="FT_ViewNewsFeed.php">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (TASK TABLE) -->
        <div class="leave-section">
            <div class="leave-header">
                <i class="fas fa-user"></i>
                <h2>Leave Management</h2>
            </div>
			<div class="leave-buttons">
                <a href="FT_ViewLeaves.php" class="leave-button">View My Leaves</a>
                <a href="FT_ApplyLeaves.php" class="leave-button">Apply For Leaves</a>
                <a href="FT_CancelLeaves.php" class="leave-button">Cancel My Leaves</a>
            </div>
        </div>
    </div>
</body>

</html>