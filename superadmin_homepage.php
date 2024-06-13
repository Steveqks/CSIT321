<?php
session_start();

$_SESSION['message1'] = "";
$_SESSION['message2'] = "";
$_SESSION['message3'] = "";
$_SESSION['message4'] = "";


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
			  <a href="#">Home</a>
			  <a href="#">Link 1</a>
			  <a href="#">Link 2</a>
			  <a href="#">Link 3</a>
			  <a href="#">Link 4</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">

            <!-- Add more content as needed -->
			
			<h2>Super Admin Homepage</h2>
			<br>
			1. <a href="superadmin_ManageAccount.php">Manage Account</a>
			<br>
			<br>
			
			2. <a href="superadmin_manageCompany_create.php">Create Company </a>
			<br>
			
			3. <a href="superadmin_manageCompany_view.php">Edit/Delete/View Company </a>
			<br>
			
			4. <a href="superadmin_manageCAdmin_approve_unreg_user.php">Approve unregistered users (Create New Company & Company Admin)</a>
			<br>
			<br>
			
			5. <a href="superadmin_manageCAdmin_create.php">Create Company Admin</a>
			<br>
			
			6. <a href="superadmin_manageCAdmin_view_delete.php">Edit/Delete/View/Activate/Suspend Company Admin</a>
			<br>
        </div>
    </div>

</body>
</html>


