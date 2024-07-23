<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

$user_id = $_SESSION['UserID'];
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

// Check if user has already clocked in and has not yet clocked out
$clockInExists = false;
$clockOutExists = false;
$onBreak = false;
if ($hasWorkToday) {
    $sql_attendance = "SELECT ClockIn, ClockOut, StartBreak, EndBreak FROM attendance WHERE UserID = ? AND DATE(ClockIn) = ?";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param("is", $user_id, $today);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();
    $attendance = $result_attendance->fetch_assoc();
    $stmt_attendance->close();

    if ($attendance) {
        $clockInExists = true;
        if (!is_null($attendance['ClockOut'])) {
            $clockOutExists = true;
        }
        if (!is_null($attendance['StartBreak']) && is_null($attendance['EndBreak'])) {
            $onBreak = true;
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
    <title>Take Break (PT)</title>
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

        .clock-button, .break-button {
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

        .clock-button:hover, .break-button:hover {
            background-color: #004d00;
        }

        .end-break-button {
            background-color: red;
        }

        .end-break-button:hover {
            background-color: darkred;
        }

        .underline {
            text-decoration: underline;
        }

        .button-container {
            margin-top: 20px;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
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

        <!-- RIGHT SECTION (TAKE BREAK) -->
        <div class="attendance-section">
            <div class="attendance-header">
                <i class="fas fa-coffee"></i>
                <h2>Take Break</h2>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($_GET['message'])): ?>
                <div class="message"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if ($hasWorkToday): ?>
                <?php if ($clockInExists && !$clockOutExists): ?>
                    <?php if (!$onBreak): ?>
                        <!-- Start Break Page -->
                        <div class="info-text">
                            You are currently working. Would you like to start your break?
                        </div>
                        <div class="button-container">
                            <a href="PT_StartBreak.php" class="break-button">Start Break</a>
                        </div>
                    <?php else: ?>
                        <!-- End Break Page -->
                        <div class="info-text">
                            You are currently on a break. Would you like to end your break?
                        </div>
                        <div class="button-container">
                            <a href="PT_EndBreak.php" class="break-button end-break-button">End Break</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Not Clocked In -->
                    <div class="info-text">
                        You need to clock in before you can take a break.
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
