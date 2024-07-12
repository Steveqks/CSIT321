<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['Email'])) {
    header("Location: ../Unregistered Users/LoginPage.php");
    exit();
}

$requestor_schedule_id = $_POST['user_shift'];
$requested_schedule_id = $_POST['available_shift'];
$requestor_user_id = $_SESSION['UserID'];

// Connect to the database
$conn = OpenCon();

// Insert new swap request
$sql_insert = "INSERT INTO swap_requests (RequestorScheduleID, RequestedScheduleID, RequestorUserID, RequestedUserID)
               VALUES (?, ?, ?, (SELECT UserID FROM schedule WHERE ScheduleID = ?))";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iiii", $requestor_schedule_id, $requested_schedule_id, $requestor_user_id, $requested_schedule_id);

if ($stmt_insert->execute()) {
    header("Location: PT_SwapShift.php?message=Swap request submitted successfully.");
} else {
    header("Location: PT_SwapShift.php?error=Failed to submit swap request.");
}

$stmt_insert->close();
CloseCon($conn);
exit();
?>
