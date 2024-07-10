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

	if(isset($_POST['submit']))
	{
		//store form variables
		
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
		$leavetype = $_POST['leavetype'];
		$Comments = $_POST['Comments'];
		$status = 0; //Unapproved by default
		
		if(isset($_POST['HalfDay']) && $_POST['HalfDay'] == "1")
		{
			$HalfDay = 1;
			// Fetch tasks for the logged-in user
			mysqli_query($conn,"INSERT INTO leaves(UserID,LeaveType,StartDate,EndDate,HalfDay,Status,Comments) VALUES ('$user_id','$leavetype','$startdate','$enddate','$HalfDay','$status','$Comments')")or die("Error Occured");
			echo "<div class='message'>
                      <p>Leave Applied Successfully!</p>
                  </div> <br>";
		}
		else
		{
			$HalfDay = 0;
			mysqli_query($conn,"INSERT INTO leaves(UserID,LeaveType,StartDate,EndDate,HalfDay,Status,Comments) VALUES ('$user_id','$leavetype','$startdate','$enddate','$HalfDay','$status','$Comments')")or die("Error Occured");
			echo "<div class='message'>
                      <p>Leave Applied Successfully!</p>
                  </div> <br>";
		}
	}
	
	CloseCon($conn);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leaves(PT)</title>
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

		.apply-leaves-section {
			padding: 20px;
			flex-grow: 1;
		}

		.apply-leaves-header {
			display: inline-flex;
			align-items: center;
			border-bottom: 1px solid black;
			padding-bottom: 5px;
			margin-bottom: 20px;
		}

		.apply-leaves-header i {
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
                <li><a href="PT_HomePage.php"><?php echo "$FirstName, Staff(PT)"?></a></li>
                <li><a href="PT_AccountDetails.php">Manage Account</a></li>
                <li><a href="PT_AttendanceManagement.php">Attendance Management</a></li>
                <li><a href="PT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="#">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="#">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (TASK TABLE) -->
        <div class="apply-leaves-section">
            <div class="apply-leaves-header">
                <i class="fas fa-user"></i>
                <h2>Apply For Leaves</h2>
			</div>
			
			<div class = "apply-leave-form">
				<form action = "" method = "post">
						<label for "startdate">Start Date: </label>
						<input id = "startdate" name = "startdate" type = "date" placeholder = "Start Date"><br><br>
						<label for "enddate">End Date: </label>
						<input id = "enddate" name = "enddate" type = "date" placeholder = "End Date"><br><br>
						<label for "leavetype">Leave Type: </label>
						<select id = "leavetype" name = "leavetype">
							<option value = "Vacation">Vacation</option>
							<option value = "Sick Leave">Sick Leave</option>
							<option value = "Personal Leave">Personal Leave</option>
						</select><br><br>
						<input id = "HalfDay" type = "checkbox" name = "HalfDay" value = "1">
						<label for "HalfDay">Half Day Leave</label><br><br>
						<label for "comments_tb">Comments: </label>
						<input id = "comments_tb" type = "textarea" name = "Comments" placeholder = "Comments"><br><br>
						<button id = "submitBtn" name = "submit">Apply Leave</button>
					</form>
            </div>
        </div>
    </div>
</body>

</html>