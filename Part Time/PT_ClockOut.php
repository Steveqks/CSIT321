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

// Check if the user has clocked in today
$sql_attendance = "SELECT ClockIn, StartBreak, EndBreak FROM attendance WHERE UserID = ? AND DATE(ClockIn) = ?";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("is", $user_id, $today);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();
$attendance = $result_attendance->fetch_assoc();
$stmt_attendance->close();

if ($attendance && is_null($attendance['ClockOut'])) {
    // Update clock-out time and calculate total hours
    $clock_out_time = date("Y-m-d H:i:s");
    $sql_update = "UPDATE attendance SET ClockOut = ?, TotalHours = (TIMESTAMPDIFF(SECOND, ClockIn, ?) - IF(StartBreak IS NOT NULL AND EndBreak IS NOT NULL, TIMESTAMPDIFF(SECOND, StartBreak, EndBreak), 0)) / 3600 WHERE UserID = ? AND DATE(ClockIn) = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssis", $clock_out_time, $clock_out_time, $user_id, $today);

    if ($stmt_update->execute()) {
        header("Location: PT_TakeAttendance.php?message=Clocked out successfully.");
    } else {
        header("Location: PT_TakeAttendance.php?error=Failed to clock out. Please try again.");
    }

    $stmt_update->close();
} else {
    header("Location: PT_TakeAttendance.php?error=You have not clocked in today or have already clocked out.");
}

CloseCon($conn);
exit();
?>
