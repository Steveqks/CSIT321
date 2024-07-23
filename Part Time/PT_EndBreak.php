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

// Check if the user has started a break today and has not ended it yet
$sql_attendance = "SELECT StartBreak, EndBreak FROM attendance WHERE UserID = ? AND DATE(ClockIn) = ?";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("is", $user_id, $today);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();
$attendance = $result_attendance->fetch_assoc();
$stmt_attendance->close();

if ($attendance && !is_null($attendance['StartBreak']) && is_null($attendance['EndBreak'])) {
    // End break
    $end_break_time = date("Y-m-d H:i:s");
    $sql_update = "UPDATE attendance SET EndBreak = ? WHERE UserID = ? AND DATE(ClockIn) = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sis", $end_break_time, $user_id, $today);

    if ($stmt_update->execute()) {
        header("Location: PT_Break.php?message=Break ended successfully.");
    } else {
        header("Location: PT_Break.php?error=Failed to end break. Please try again.");
    }

    $stmt_update->close();
} else {
    header("Location: PT_Break.php?error=You have not started a break or have already ended it today.");
}

CloseCon($conn);
exit();
?>
