<!-- PHP CODE HERE -->
<?php
	session_start();
	include 'db_connection.php';
			
	// Connect to the database
	$conn = OpenCon();

	// Select the Top 3 5* reviews ordered by latest review time
	$sql = "SELECT a.UserID, a.ReviewTitle, a.Rating, a.comments, a.DatePosted, b.UserID, b.CompanyID, b.FirstName, b.LastName, c.CompanyID, c.CompanyName FROM reviews a
		   INNER JOIN existinguser as b
		   ON a.UserID = b.UserID
		   INNER JOIN company as c
           ON b.CompanyID = c.CompanyID
		   WHERE a.Rating = 5
           ORDER BY a.DatePosted DESC
           LIMIT 3";

	
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();
	$top3reviews = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
	
	#Limit the first 3 features for home page
	$features_sql = "SELECT Name, Icon from Features LIMIT 3";
	
	$stmts = $conn->prepare($features_sql);
	$stmts->execute();
	$results = $stmts->get_result();
	$features = $results->fetch_all(MYSQLI_ASSOC);
	$stmts->close();
	
	// Close the database connection
	CloseCon($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel = "stylesheet" href="HomePage.css">
</head>
<body>
   <nav class="navbar">
            <div class="navdiv">
                <div class="logo"><a href="HomePage.php"><img id = "teamlogo" accesskey=""src = "Images/tms.png"></a></div>
			     <ul>
					<li><a href="Features.php">Features</a></li>
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
		<div id = "Features">
			<?php if (count($features) > 0): ?>
				<?php foreach ($features as $feature): ?>
					<div id = "FeatureBox">
						<img src = <?php echo htmlspecialchars($feature['Icon'])?> width = "256px" height = "256px">
						<h2><?php echo htmlspecialchars($feature['Name'])?></h2>
					</div>
				<?php endforeach ?>
			<?php else: ?>
				<h2 style = "text-align:center"> No Plans Available </h2>
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


	
</body>
</html>