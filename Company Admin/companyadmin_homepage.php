<?php
session_start();

$_SESSION['message'] = "";
$_SESSION['message0'] = "";
$_SESSION['message1'] = "";
$_SESSION['message2'] = "";
$_SESSION['message3'] = "";
$_SESSION['message4'] = "";
$_SESSION['message5'] = "";
$_SESSION['message6'] = "";


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="a.css">

    <title>TrackMySchedule</title>
</head>
<body>

    <!-- Top Section -->
	<div style="border: 1px solid black; height: 20vh; overflow: hidden; text-align: left;">
        <img src="tms.png" alt="TrackMySchedule Logo" style="height: 100%; width: auto;">
    </div>

    <!-- Middle Section -->
    <div style="display: flex; border: 1px solid black; height: 80vh;">
        
        <!-- Left Section (Navigation) -->
			<div class="vertical-menu" style="border-right: 1px solid black; padding: 0px;">
				<a href="companyadmin_homepage.php">Home</a>
				<a href="companyadmin_ManageAccount.php">Manage Account</a>
				<a href="companyadmin_ManageUserAccounts_create.php">Manage User Accounts > Create</a>
				<a href="companyadmin_ManageUserAccounts_view.php">Manage User Accounts > View</a>
				<a href="companyadmin_specialisation_create.php">Manage Specialisation > Create </a>
				<a href="companyadmin_specialisation_view_delete.php">Manage Specialisation > View</a>
				<a href="companyadmin_teamManagement_create.php">Manage Team > Create </a>
				<a href="companyadmin_teamManagement_view_delete.php">Manage Team > View</a>
				<a href="Logout.php">Logout</a>

			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">

            <!-- Add more content as needed -->
			<h2>Company Admin Homepage</h2>
			
			<br>
			1.  <a href="companyadmin_ManageAccount.php">Manage Account</a>
			<br>
			<br>
			
			2.  <a href="companyadmin_ManageUserAccounts_create.php">Create User Accounts</a>
			<br>
			
			3.  <a href="companyadmin_ManageUserAccounts_view.php">View/Edit/Delete User Accounts</a>
			<br>
			<br>
			
			4.  <a href="companyadmin_specialisation_create.php">Create Specialisation</a>
			<br>
			
			5.  <a href="companyadmin_specialisation_view_delete.php">View/Edit/Delete/Activate/Suspend Specialisation</a>
			<br>
			<br>
			
			6. 	<a href="companyadmin_teamManagement_create.php">Create Team</a>
			<br>
			
			7. 	<a href="companyadmin_teamManagement_view_delete.php">View/Edit/Delete Team</a>
			
			
        </div>
    </div>

</body>
</html>


