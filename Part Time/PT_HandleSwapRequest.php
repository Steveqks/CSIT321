<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

$request_id = $_POST['request_id'];
$action = $_POST['action'];

// Connect to the database
$conn = OpenCon();

if ($action === 'approve') {
    // Get the schedule IDs and user IDs for the swap request
    $sql_approve = "SELECT RequestorScheduleID, RequestedScheduleID, 
                    (SELECT UserID FROM schedule WHERE ScheduleID = RequestorScheduleID) AS RequestorUserID,
                    (SELECT UserID FROM schedule WHERE ScheduleID = RequestedScheduleID) AS RequestedUserID
                    FROM swap_requests WHERE SwapRequestID = ?";
    $stmt_approve = $conn->prepare($sql_approve);
    $stmt_approve->bind_param("i", $request_id);
    $stmt_approve->execute();
    $stmt_approve->bind_result($requestor_schedule_id, $requested_schedule_id, $requestor_user_id, $requested_user_id);
    $stmt_approve->fetch();
    $stmt_approve->close();

    if ($requestor_schedule_id && $requested_schedule_id) {
        // Update the schedules
        $sql_update_schedules = "UPDATE schedule SET UserID = CASE 
                                    WHEN ScheduleID = ? THEN ?
                                    WHEN ScheduleID = ? THEN ?
                                 END
                                 WHERE ScheduleID IN (?, ?)";
        $stmt_update_schedules = $conn->prepare($sql_update_schedules);
        $stmt_update_schedules->bind_param("iiiiii", $requested_schedule_id, $requestor_user_id, $requestor_schedule_id, $requested_user_id, $requestor_schedule_id, $requested_schedule_id);
        $stmt_update_schedules->execute();
        $stmt_update_schedules->close();

        // Update the swap request status to 'Approved'
        $sql_update_request = "UPDATE swap_requests SET Status = 'Approved' WHERE SwapRequestID = ?";
        $stmt_update_request = $conn->prepare($sql_update_request);
        $stmt_update_request->bind_param("i", $request_id);
        $stmt_update_request->execute();
        $stmt_update_request->close();

        header("Location: PT_ViewSwapRequests.php?message=Swap request approved successfully.");
    } else {
        header("Location: PT_ViewSwapRequests.php?error=Failed to approve swap request.");
    }
} elseif ($action === 'reject') {
    // Reject the swap request
    $sql_reject = "UPDATE swap_requests SET Status = 'Rejected' WHERE SwapRequestID = ?";
    $stmt_reject = $conn->prepare($sql_reject);
    $stmt_reject->bind_param("i", $request_id);

    if ($stmt_reject->execute()) {
        header("Location: PT_ViewSwapRequests.php?message=Swap request rejected successfully.");
    } else {
        header("Location: PT_ViewSwapRequests.php?error=Failed to reject swap request.");
    }

    $stmt_reject->close();
}

CloseCon($conn);
exit();
?>
