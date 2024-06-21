<?php
session_start();
include 'db_connection.php';

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

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

// Get today's date
$today = date("Y-m-d");

// Check if user has work today
$sql_schedule = "SELECT StartWork, EndWork FROM schedule WHERE UserID = ? AND WorkDate = ?";
$stmt_schedule = $conn->prepare($sql_schedule);
$stmt_schedule->bind_param("is", $user_id, $today);
$stmt_schedule->execute();
$result_schedule = $stmt_schedule->get_result();
$schedule = $result_schedule->fetch_assoc();
$stmt_schedule->close();

$hasWorkToday = $schedule ? true : false;

// Check if user has already clocked in
$clockInExists = false;
$clockOutExists = false;
if ($hasWorkToday) {
    $sql_attendance = "SELECT ClockIn, ClockOut FROM attendance WHERE UserID = ? AND DATE(ClockIn) = ?";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param("is", $user_id, $today);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();
    $attendance = $result_attendance->fetch_assoc();
    $stmt_attendance->close();

    if ($attendance) {
        $clockInExists = true;
        if ($attendance['ClockOut'] != null) {
            $clockOutExists = true;
        }
    }
}

CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance (PT)</title>
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

        .info-text {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .clock-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: green;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 10px;
            border: 1px solid black;
            transition: background-color 0.3s, color 0.3s;
        }

        .clock-button:hover {
            background-color: #004d00;
        }
        
        .underline {
            text-decoration: underline;
        }
		
		.button-container {
			margin-top: 20px;
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
                <li><a href="#">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="#">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="#">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (TAKE ATTENDANCE) -->
        <div class="attendance-section">
            <div class="attendance-header">
                <i class="fas fa-clock"></i>
                <h2>Take Attendance</h2>
            </div>
            
            <?php if ($hasWorkToday): ?>
                <?php if (!$clockInExists): ?>
                    <!-- Clock In Page -->
                    <div class="info-text">
                        Your shift today starts at <span class="underline"><?php echo date("H:i", strtotime($schedule['StartWork'])); ?>h</span> and ends at <span class="underline"><?php echo date("H:i", strtotime($schedule['EndWork'])); ?>h</span>.
                    </div>
                    <div>
						The time now is: <span class="underline"><?php echo date("H:i"); ?>h.</span>
					</div>
					<div class="button-container">
						<a href="PT_ClockIn.php" class="clock-button">Clock In</a>
					</div>
                <?php elseif ($clockInExists && !$clockOutExists): ?>
                    <!-- Clock Out Page -->
                    <div class="info-text">
                        You have already clocked in today.
                    </div>
                    <div>
						The time now is: <span class="underline"><?php echo date("H:i"); ?>h.</span>
					</div>
					<div class="button-container">
						<a href="PT_ClockOut.php" class="clock-button">Clock Out</a>
					</div>
                <?php else: ?>
                    <!-- Already Clocked Out -->
                    <div class="info-text">
                        You have already clocked in and out today.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Work Today -->
                <div class="info-text">
                    You have no scheduled work today.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
