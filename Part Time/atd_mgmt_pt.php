<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// $user_id = $_SESSION['user_id'];

// Connect to the database
$conn = OpenCon();

// Close the database connection
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management (PT)</title>
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

        .management-section {
            padding: 20px;
            flex-grow: 1;
        }

        .management-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .management-header i {
            margin-right: 10px;
        }

        .management-header h2 {
            margin: 0;
        }

        .management-buttons {
            display: flex;
            flex-direction: column;
        }

        .management-button {
            display: inline-block;
            width: 200px;
            padding: 15px 20px;
            margin-bottom: 10px;
            background-color: #d3d3d3;
            color: black;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s, color 0.3s;
            border: none;
            border-radius: 0;
        }

        .management-button:hover {
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
                <li><a href="#">name, Staff (PT)</a></li>
                <li><a href="#">Manage Account</a></li>
                <li><a href="#">Attendance Management</a></li>
                <li><a href="#">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="#">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="#">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (ATTENDANCE MANAGEMENT) -->
        <div class="management-section">
            <div class="management-header">
                <i class="fas fa-calendar-check"></i>
                <h2>Attendance Management</h2>
            </div>
            <div class="management-buttons">
                <a href="view_atd_pt.php" class="management-button">View Attendance</a>
                <a href="take_atd_pt.php" class="management-button">Take Attendance</a>
                <a href="break_pt.php" class="management-button">Start / End Break</a>
            </div>
        </div>
    </div>
</body>
</html>
