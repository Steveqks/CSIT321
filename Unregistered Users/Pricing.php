<?php
	session_start();
	include 'db_connection.php';
			
	// Connect to the database
	$conn = OpenCon();

	// Fetch all the available plans from the plans database
	$sql = "SELECT PlanName, Price, UserAccess, CustomerSupport from plans";
	
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();
	$plans = $result->fetch_all(MYSQLI_ASSOC);

	// Close the database connection
	$stmt->close();
	CloseCon($conn);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel = "stylesheet" href="Pricing.css">
</head>
<body>
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
    <div id = "PricingTitleContainer">
        <h2>Choose the best plan for your team</h2>
    </div>
    <div id = "PricingContainer">
		<?php foreach ($plans as $plan): ?>
			<div id = "PlanBox" style = "background-color: lightblue">
				<h3><?php echo htmlspecialchars($plan['PlanName']); ?></h3>
				<h4>($<?php echo htmlspecialchars($plan['Price']); ?>/month)</h4>
				<h3>User Access</h3>
				<h4>Up to <?php echo htmlspecialchars($plan['UserAccess']); ?> Users</h4>
				<h3>Customer Support</h3>
				<h4><?php echo htmlspecialchars($plan['CustomerSupport']); ?></h4>
			</div>
		<?php endforeach ?>
    </div>
    <footer>
        
    </footer>
</body>
</html>