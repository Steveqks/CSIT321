<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['Email'])) {
    header("Location: ../Unregistered Users/LoginPage.php");
    exit();
}

$user_id = $_SESSION['UserID'];
$Email = $_SESSION['Email'];
$FirstName = $_SESSION['FirstName'];
$specialisation_id = $_SESSION['SpecialisationID'];

// Connect to the database
$conn = OpenCon();

// Fetch user's shifts
$sql_user_shifts = "SELECT ScheduleID, WorkDate, StartWork, EndWork FROM schedule WHERE UserID = ? AND WorkDate >= CURDATE()";
$stmt_user_shifts = $conn->prepare($sql_user_shifts);
$stmt_user_shifts->bind_param("i", $user_id);
$stmt_user_shifts->execute();
$result_user_shifts = $stmt_user_shifts->get_result();
$user_shifts = $result_user_shifts->fetch_all(MYSQLI_ASSOC);
$stmt_user_shifts->close();

$has_shifts = count($user_shifts) > 0;

// Fetch available shifts for swap
$available_shifts = [];
$selected_shift_id = isset($_POST['user_shift']) ? $_POST['user_shift'] : null;

if ($selected_shift_id) {
    // Check for existing pending swap requests for the selected shift or any shift involved in a pending swap
    $sql_check_pending = "SELECT COUNT(*) AS pending_count FROM swap_requests 
                          WHERE (RequestorScheduleID = ? OR RequestedScheduleID = ?)
                          AND Status = 'Pending'";
    $stmt_check_pending = $conn->prepare($sql_check_pending);
    $stmt_check_pending->bind_param("ii", $selected_shift_id, $selected_shift_id);
    $stmt_check_pending->execute();
    $result_check_pending = $stmt_check_pending->get_result();
    $pending_request = $result_check_pending->fetch_assoc();
    $stmt_check_pending->close();

    if ($pending_request['pending_count'] > 0) {
        header("Location: PT_SwapShift.php?error=A swap request for this shift is already pending.");
        exit();
    }

    $sql_available_shifts = "SELECT s.ScheduleID, s.WorkDate, s.StartWork, s.EndWork, e.FirstName, e.LastName
                             FROM schedule s
                             INNER JOIN existinguser e ON s.UserID = e.UserID
                             WHERE s.WorkDate >= CURDATE() AND s.UserID != ? AND e.SpecialisationID = ? AND e.Role = 'PT'
                             AND s.ScheduleID NOT IN (
                                 SELECT RequestorScheduleID FROM swap_requests WHERE Status = 'Pending'
                                 UNION
                                 SELECT RequestedScheduleID FROM swap_requests WHERE Status = 'Pending'
                             )";
    $stmt_available_shifts = $conn->prepare($sql_available_shifts);
    $stmt_available_shifts->bind_param("ii", $user_id, $specialisation_id);
    $stmt_available_shifts->execute();
    $result_available_shifts = $stmt_available_shifts->get_result();
    $available_shifts = $result_available_shifts->fetch_all(MYSQLI_ASSOC);
    $stmt_available_shifts->close();
}

CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Shift</title>
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

        .swap-section {
            padding: 20px;
            flex-grow: 1;
        }

        .swap-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .swap-header i {
            margin-right: 10px;
        }

        .swap-header h2 {
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group button, .view-requests a {
            display: inline-block;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .form-group button:hover, .view-requests a:hover {
            background-color: darkgreen;
        }

        .no-shifts-message, .no-available-shifts-message {
            font-size: 18px;
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .view-requests {
            margin-top: 20px;
        }

        .view-requests a {
            background-color: blue;
            font-size: 16px;
            padding: 10px 20px;
        }

        .view-requests a:hover {
            background-color: darkblue;
        }

        .message {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
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
                <li><a href="PT_HomePage.php"><?php echo htmlspecialchars("$FirstName, Staff(PT)"); ?></a></li>
                <li><a href="PT_AccountDetails.php">Manage Account</a></li>
                <li><a href="PT_AttendanceManagement.php">Attendance Management</a></li>
                <li><a href="PT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="PT_ViewNewsFeed.php">View News Feed</a></li>
                <li><a href="PT_SwapShift.php">Swap Shifts</a></li>
                <li><a href="#">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- RIGHT SECTION (SWAP SHIFT FORM) -->
        <div class="swap-section">
            <div class="swap-header">
                <i class="fas fa-exchange-alt"></i>
                <h2>Swap Shifts</h2>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($_GET['message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if ($has_shifts): ?>
                <form action="PT_SwapShift.php" method="post">
                    <div class="form-group">
                        <label for="user_shift">Your Shift</label>
                        <select name="user_shift" id="user_shift" required onchange="this.form.submit()">
                            <option value="">Select your shift</option>
                            <?php foreach ($user_shifts as $shift): ?>
                                <option value="<?php echo $shift['ScheduleID']; ?>" <?php echo $selected_shift_id == $shift['ScheduleID'] ? 'selected' : ''; ?>>
                                    <?php echo $shift['WorkDate'] . ' (' . $shift['StartWork'] . ' - ' . $shift['EndWork'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($selected_shift_id): ?>
                        <?php if (count($available_shifts) > 0): ?>
                            <div class="form-group">
                                <label for="available_shift">Available Shifts</label>
                                <select name="available_shift" id="available_shift" required>
                                    <option value="">Select an available shift to swap with</option>
                                    <?php foreach ($available_shifts as $shift): ?>
                                        <option value="<?php echo $shift['ScheduleID']; ?>">
                                            <?php echo $shift['WorkDate'] . ' (' . $shift['StartWork'] . ' - ' . $shift['EndWork'] . ') - ' . $shift['FirstName'] . ' ' . $shift['LastName']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="no-available-shifts-message">There are no available shifts to swap with.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!isset($selected_shift_id) || count($available_shifts) > 0): ?>
                        <div class="form-group">
                            <button type="submit" formaction="PT_CreateSwapRequest.php">Request Swap</button>
                        </div>
                    <?php endif; ?>
                </form>
                <div class="view-requests">
                    <a href="PT_ViewSwapRequests.php">View Swap Requests</a>
                </div>
            <?php else: ?>
                <div class="no-shifts-message">You have no shifts scheduled.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
