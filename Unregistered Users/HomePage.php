<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel = "stylesheet" href="HomePage.css">
</head>
<body>
   <!-- PHP CODE HERE -->
   <?php
		session_start();
   	
		//connect to the database
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

   ?>
   <nav class="navbar">
            <div class="navdiv">
                <div class="logo"><a href="HomePage.php"><img id = "teamlogo" accesskey=""src = "Images/tms.png"></a></div>
			     <ul>
					<li><a href="#">Features</a></li>
				    <li><a href="AboutUs.php">About Us</a></li>
				    <li><a href="Pricing.php">Pricing</a></li>
                    <button class = "LoginBtn"><a href="LoginPage.php">Log In</a></button>
			     </ul>
            </div>
    </nav>
	<div id = "AdvertisingContainer1">
        <h2>Project Management, Simplified</h2>
		<h3>Create Projects with the best people for the role</h3>
		<button class = "EnterCredBtn"><a href="EnterCredentials.php">Get Started</a></button>
    </div>
	<div id = "AdvertisingContainer2">
		<h2>Why TrackMySchedule?</h2>
	</div>
</body>
</html>