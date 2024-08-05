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
    // Get the scheduled end work time
    // Assuming you have a way to fetch the scheduled end work time from the schedule table
    $sql_schedule = "SELECT EndWork FROM schedule WHERE UserID = ? AND WorkDate = ?";
    $stmt_schedule = $conn->prepare($sql_schedule);
    $stmt_schedule->bind_param("is", $user_id, $today);
    $stmt_schedule->execute();
    $result_schedule = $stmt_schedule->get_result();
    $schedule = $result_schedule->fetch_assoc();
    $stmt_schedule->close();

    if ($schedule) {
        $scheduled_end_work = $schedule['EndWork'];
        $clock_out_time = date("Y-m-d H:i:s");

        // Calculate overtime hours
        $clockOutTime = new DateTime($clock_out_time);
        $scheduledEndWorkTime = new DateTime($scheduled_end_work);
        
        if ($clockOutTime > $scheduledEndWorkTime) {
            $interval = $clockOutTime->diff($scheduledEndWorkTime);
            $overtime_hours = $interval->h + ($interval->i / 60);
        } else {
            $overtime_hours = 0;
        }

        // Update clock-out time and calculate total hours including overtime
        $sql_update = "UPDATE attendance 
                       SET ClockOut = ?, 
                           TotalHours = ROUND((TIMESTAMPDIFF(SECOND, ClockIn, ?) - IF(StartBreak IS NOT NULL AND EndBreak IS NOT NULL, TIMESTAMPDIFF(SECOND, StartBreak, EndBreak), 0)) / 3600, 2), 
                           NumOfOverTimeHours = ROUND(?, 2) 
                       WHERE UserID = ? AND DATE(ClockIn) = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssdis", $clock_out_time, $clock_out_time, $overtime_hours, $user_id, $today);

        if ($stmt_update->execute()) {
            header("Location: PT_TakeAttendance.php?message=Clocked out successfully.");
        } else {
            header("Location: PT_TakeAttendance.php?error=Failed to clock out. Please try again.");
        }

        $stmt_update->close();
    } else {
        header("Location: PT_TakeAttendance.php?error=No scheduled end work time found for today.");
    }
} else {
    header("Location: PT_TakeAttendance.php?error=You have not clocked in today or have already clocked out.");
}

CloseCon($conn);
exit();
?>
