<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

$user_id = $_SESSION['UserID'];

// Connect to the database
$conn = OpenCon();

// Get today's date
$today = date("Y-m-d");

// Check if the user has work scheduled today
$sql_schedule = "SELECT StartWork, EndWork FROM schedule WHERE UserID = ? AND WorkDate = ?";
$stmt_schedule = $conn->prepare($sql_schedule);
$stmt_schedule->bind_param("is", $user_id, $today);
$stmt_schedule->execute();
$result_schedule = $stmt_schedule->get_result();
$schedule = $result_schedule->fetch_assoc();
$stmt_schedule->close();

if ($schedule) {
    // Check if the user has already clocked in today
    $sql_attendance = "SELECT COUNT(*) FROM attendance WHERE UserID = ? AND DATE(ClockIn) = ?";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param("is", $user_id, $today);
    $stmt_attendance->execute();
    $stmt_attendance->bind_result($count);
    $stmt_attendance->fetch();
    $stmt_attendance->close();

    if ($count == 0) {
        // Insert clock-in record with NULL values for ClockOut, StartBreak, and EndBreak
        $clock_in_time = date("Y-m-d H:i:s");
        $sql_insert = "INSERT INTO attendance (UserID, ClockIn, ClockOut, StartBreak, EndBreak, TotalHours) VALUES (?, ?, NULL, NULL, NULL, NULL)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("is", $user_id, $clock_in_time);

        if ($stmt_insert->execute()) {
            header("Location: PT_TakeAttendance.php?message=Clocked in successfully.");
        } else {
            header("Location: PT_TakeAttendance.php?error=Failed to clock in. Please try again.");
        }

        $stmt_insert->close();
    } else {
        header("Location: PT_TakeAttendance.php?error=You have already clocked in today.");
    }
} else {
    header("Location: PT_TakeAttendance.php?error=No work scheduled for today.");
}

CloseCon($conn);
exit();
?>
