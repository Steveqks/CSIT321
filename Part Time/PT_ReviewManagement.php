<?php
	session_start();
	include 'db_connection.php';
	include '../Session/session_check_user_PT.php';

	$user_id = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$FirstName = $_SESSION['FirstName'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management (PT)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
		body 
		{
			margin: 0;
			font-family: Arial, sans-serif;
		}
		.top-section 
		{
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
			height: 80vh;
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

		.review-section 
		{
			padding: 20px;
			flex-grow: 1;
		}

		.review-header 
		{
			display: inline-flex;
			align-items: center;
			border-bottom: 1px solid black;
			padding-bottom: 5px;
			margin-bottom: 20px;
		}

		.review-header i 
		{
			margin-right: 10px;
		}
		
		.review-header h2 {
            margin: 0;
        }

        .review-buttons {
            display: flex;
            flex-direction: column;
        }
		
		.review-button {
            display: inline-block;
            width: 200px; /* Adjust the width to make buttons shorter */
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
		
		.review-button:hover {
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
        <?php include 'navbar.php'; ?>
        
        <!-- RIGHT SECTION (REVIEW TABLE) -->
        <div class="review-section">
            <div class="review-header">
                <i class="fas fa-user"></i>
                <h2>Review Management</h2>
            </div>
			<div class="review-buttons">
                <a href="PT_SubmitReview.php" class="review-button">Submit a Review</a>
                <a href="PT_EditReview.php" class="review-button">Edit a Review</a>
            </div>
        </div>
    </div>
</body>

</html>