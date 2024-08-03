<!-- PHP CODE HERE -->
<?php
	session_start();
	include 'db_connection.php';
			
	// Connect to the database
	$conn = OpenCon();

	#Select the Name, Description and Image for all the available features 
	$sql = "SELECT Name, Description, Image from features";
	
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();
	$features = $result->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
	
	// Close the database connection
	CloseCon($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel = "stylesheet" href="Features.css">
</head>
<body>
   <nav class="navbar">
            <div class="navdiv">
                <div class="logo"><a href="HomePage.php"><img id = "teamlogo" accesskey=""src = "Images/tms.png"></a></div>
			     <ul>
					<li><a href="Features.php">Features</a></li>
				    <li><a href="Pricing.php">Pricing</a></li>
                    <button class = "LoginBtn"><a href="LoginPage.php">Log In</a></button>
			     </ul>
            </div>
    </nav>
	
	<div id = "TitleContainer">
        <h2>Here's What We have to offer</h2>
    </div>
	<!-- Display all the features from the database -->
	<?php if (count($features) > 0): ?>
		<?php foreach ($features as $feature): ?>
			<div id = "Features">
				<h2 style = "padding-top: 20px"><?php echo htmlspecialchars($feature['Name'])?></h2>
				<img src = <?php echo htmlspecialchars($feature['Image'])?> width = "948px" height = "710px">
				<h2 style = "padding-bottom: 20px"><?php echo htmlspecialchars($feature['Description'])?></h2>
			</div>
		<?php endforeach ?>
	<?php else: ?>
		<h2 style = "text-align:center"> No Features Available </h2>
	<?php endif; ?>
	
	<!-- Footer -->
	<footer>&#169;TrackMySchedule, Icons taken from FlatIcon & Freepik</footer>
</body>
</html>