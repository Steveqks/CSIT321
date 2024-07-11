<?php
session_start();

$_SESSION['message'] = "";
$_SESSION['message1'] = "";
$_SESSION['message2'] = "";
$_SESSION['message3'] = "";
$_SESSION['message4'] = "";
$_SESSION['message5'] = "";
$_SESSION['message6'] = "";
//$currentDateTime = date('Y-m-d H:i:s');
//echo $currentDateTime;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">

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
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">

            <!-- Add more content as needed -->
			<h3><u> HomePage </u></h3>
			<?php
			echo "<b>Welcome, </b><br>" . $_SESSION['FirstName'] . "(" . $_SESSION['Role']  .  ")";
			?>
			
        </div>
    </div>

</body>
</html>


