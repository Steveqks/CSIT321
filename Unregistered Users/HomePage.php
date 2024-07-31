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
		
		
		//Insert User credentials
		if(isset($_POST['submit']))
		{
			
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$companyname = $_POST['companyname'];
			$planschoice = $_POST['plans'];
			
			$verify_query = mysqli_query($db, "SELECT email FROM unregisteredusers where Email = '$email'");
			
			if(mysqli_num_rows($verify_query) != 0)
			{
				echo "<div class='message'>
                      <p>This email is used, Try another One Please!</p>
                  </div> <br>";
			}
			else
			{
				mysqli_query($db,"INSERT INTO unregisteredusers(Email,Password,CompanyName,FirstName,LastName,PlanID) VALUES('$email','$password','$companyname','$firstname','$lastname','$planschoice')") or die("Error Occured");

				echo "<div class='message'>
                      <p>Registration successfully!</p>
                  </div> <br>";
			}
			
		}
		
    
    
    
   ?>
   <nav class="navbar">
            <div class="navdiv">
                <div class="logo"><a href="HomePage.php"><img id = "teamlogo" accesskey=""src = "Images/tms.png"></a></div>
			     <ul>
				    <li><a href="AboutUs.php">About Us</a></li>
				    <li><a href="Pricing.php">Pricing</a></li>
                    <button class = "LoginBtn"><a href="LoginPage.php">Log In</a></button>
			     </ul>
            </div>
    </nav>
    <div class = "grid-container">
        <div class="grid-item">
               <div id = "RegisterForm">
                    <h2>Empower Your Team, Simplify Your Schedule</h2>
					<form action = "" method = "post">
						<input id = "firstname" name = "firstname" type = "text" placeholder = "First Name">
						<input id = "lastname" name = "lastname" type = "text" placeholder = "Last Name">
						<input id = "email" name = "email" type = "text" placeholder = "Email Address">
						<input id = "password" name = "password" type = "password" placeholder = "Password">
						<input id = "companyname" name = "companyname" type = "text" placeholder = "Company Name">
						<select id = "planschoice" name = "plans">
							<option value = "1">Tier 1($9.99/month)</option>
							<option value = "2">Tier 2($29.99/month)</option>
							<option value = "3">Tier 3($59.99/month)</option>
						</select>
						<button id = "submitBtn" name = "submit">Register Now</button>
					</form>
                </div>
            </div>
        <div class="grid-item">
            <img id = "group" src = "Images/group.png">
        </div>
    </div>
<<<<<<< Updated upstream
    <footer>
        
    </footer>
=======
	
	<div id = "AdvertisingContainer2">
		<h2>Why TrackMySchedule?</h2>
		<div id = "Features">
			<?php if (count($features) > 0): ?>
				<?php foreach ($features as $feature): ?>
					<div id = "FeatureBox">
						<img src = <?php echo htmlspecialchars($feature['Icon'])?> width = "256px" height = "256px">
						<h2><?php echo htmlspecialchars($feature['Name'])?></h2>
					</div>
				<?php endforeach ?>
			<?php else: ?>
				<h2 style = "text-align:center"> No Features Available </h2>
		    <?php endif; ?>
		</div>
	</div>
	
	<div id = "AdvertisingContainer3">
		<h2>Unsure how to Allocate Tasks to team members?</h2>
		<h3>TrackMySchedule has you covered!</h3>
		<img src = "Images/Placeholder.jpeg" width = "948px" height = "710px">
	</div>
	
		<!-- Display reviews section -->
	<div id = "AdvertisingContainer4">
		<h2>Dont just listen to us, hear from our users!</h2>
		<?php if (count($top3reviews) > 0): ?>
			<?php foreach ($top3reviews as $review): ?>
				<div class = 'displayReview'>
					<p><?php echo htmlspecialchars($review['ReviewTitle']); ?></p>
					<p>"<?php echo htmlspecialchars($review['comments']); ?>"</p>
					<!-- Store the user rating, convert to stars -->
					<?php
						$rating = (int)$review['Rating'];
						echo "<div class = 'rating'>";
						for($i = 1; $i <= 5; $i++)
						{
							if ($i <= $rating)
							{
								 echo "<span class='filled'>&#9733;</span>"; // Filled star
							}
							else
							{
								echo "<span class='unfilled'>&#9733;</span>"; // Unfilled star
							}
						}
						echo "</div>";
					?>
					<!-- FirstName, LastName, Company -->
					<p><?php echo htmlspecialchars($review['FirstName']); ?> <?php echo htmlspecialchars($review['LastName']); ?> , <?php echo htmlspecialchars($review['CompanyName']); ?></p>
				</div>
			<?php endforeach ?>
		    <?php else: ?>
				<h2 style = "text-align:center"> No Reviews Available </h2>
		    <?php endif; ?>
			
	</div>
	<!-- Footer -->
	<footer>&#169;TrackMySchedule, Icons taken from FlatIcon & Freepik</footer>


	
>>>>>>> Stashed changes
</body>
</html>