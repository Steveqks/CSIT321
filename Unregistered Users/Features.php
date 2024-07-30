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
	<link rel = "stylesheet" href="Features.css">
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
	
	<div id = "TitleContainer">
        <h2>Here's What We have to offer</h2>
    </div>
	
	
</body>
</html>