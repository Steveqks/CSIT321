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

// Fetch user details
$sql = "SELECT FirstName, LastName, Email FROM existinguser WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the database connection
$stmt->close();
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Details (PT)</title>
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

        .details-section {
            padding: 20px;
            flex-grow: 1;
        }

        .details-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .details-header i {
            margin-right: 10px;
        }

        .details-header h2 {
            margin: 0;
        }

        .message {
            color: green;
			margin-top: 20px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .details {
            font-size: 1.2em;
        }

        .details p {
            margin: 10px 0;
        }

        .details span {
            font-weight: bold;
            text-decoration: underline;
        }

        .edit-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #d3d3d3;
            color: black;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            border: none;
        }

        .edit-button:hover {
            background-color: #bbb;
            color: black;
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
        
        <!-- RIGHT SECTION (USER DETAILS) -->
        <div class="details-section">
            <div class="details-header">
                <i class="fas fa-user"></i>
                <h2>Your Account Details</h2>
            </div>

            <div class="details">
                <p>Full Name: <span><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></span></p>
                <p>Email: <span><?php echo htmlspecialchars($user['Email']); ?></span></p>
                <a href="PT_EditAccountDetails.php" class="edit-button">Edit Account Details</a>
            </div>
			
			<!-- Display success message if exists -->
            <?php if (isset($_GET['message'])): ?>
                <div class="message"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
