<?php
	session_start();
	include 'db_connection.php';

	// Check if user is logged in
	include '../Session/session_check_user_FT.php';

	$user_id = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$FirstName = $_SESSION['FirstName'];
			
	// Connect to the database
	$conn = OpenCon();

	$sql = "SELECT * FROM reviews WHERE UserID = $user_id";
	
	$stmt = $conn->prepare($sql);
	//$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$review = $result->fetch_assoc();
	// Close the database connection
	$stmt->close();
	CloseCon($conn);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Details (PT)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
		body {
            margin: 0;
            font-family: Arial, sans-serif;
		}

		.top-section {
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

		.review-section {
			padding: 20px;
			flex-grow: 1;
		}

		.review-header {
			display: inline-flex;
			align-items: center;
			border-bottom: 1px solid black;
			padding-bottom: 5px;
			margin-bottom: 20px;
		}

		.review-header i {
			margin-right: 10px;
		}
		
		.rating {
			margin: 10px 0;
			display: flex;
			/*justify-content: center;*/
		}
		
		label
		{
			font-weight: bold;
		}
		.star {
			width: 32px;
			font-size: 32px;
			cursor: pointer;
			color: #ccc;
		}

		.star:hover,
		.star.selected {
			color: gold;
		}
		
		#reviewtitle
		{
			width: 200px;
		}
		
		#reviewcomments
		{
			width: 200px;
			height: 40px;
		}
		#submitBtn
		{
			width: 150px;
			height: 50px;
			border: none;
			background-color: #28a745;
            color: white;
		}
        #submitBtn:hover {
            background-color: #218838;
            color: white;
        }
		.error-message {
            color: red;
        }
		
		.success-message {
			color: green;
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
        <div class="navbar">
            <ul>
                <li><a href="FT_HomePage.php"><?php echo "$FirstName, Staff(FT)"?></a></li>
                <li><a href="FT_AccountDetails.php">Manage Account</a></li>
                <li><a href="FT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="FT_ViewNewsFeed.php">View News Feed</a></li>
				<li><a href="FT_ReviewManagement.php">Leave a Review!</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (TASK TABLE) -->
        <div class="review-section">
            <div class="review-header">
                <i class="fas fa-user"></i>
                <h2>Edit A Review</h2>
			</div>
			<div class = "review-form">
				<?php if($review): ?>
					<form action = "FT_UpdateReview.php" method = "post">
						<label for "reviewtitle">Title: </label><br>
						<input id = "reviewtitle" name = "reviewtitle" type = "text" value="<?php echo htmlspecialchars($review['ReviewTitle']); ?>"><br><br>
						
						
						<label for "rating">Rating: </label>
						<div class="rating">
							<span class="star" data-value="1">&#9733;</span>
							<span class="star" data-value="2">&#9733;</span>
							<span class="star" data-value="3">&#9733;</span>
							<span class="star" data-value="4">&#9733;</span>
							<span class="star" data-value="5">&#9733;</span>
						</div>
						
						<input type="hidden" name="rating" id="rating" value = "<?php echo ($review['Rating']); ?>" required>
						
						<label for "reviewcomments">Comments: </label><br>
						<input id = "reviewcomments" name = "reviewcomments" type = "textarea" value="<?php echo htmlspecialchars($review['Comments']); ?>"><br><br>
						<button id = "submitBtn" name = "submit">Edit Review</button>
						<?php
						if (isset($_GET['message'])) {
							echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
						} elseif (isset($_GET['error'])) {
							echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
						}
					?>
					</form>
				<?php else: ?>
					<p>You have yet to submit a review, please submit a review first!!</p>
				<?php endif; ?>
            </div>
        </div>
    </div>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const stars = document.querySelectorAll('.star');
			const ratingInput = document.getElementById('rating');
			
			// Set initial rating if any
			const initialRating = parseInt(ratingInput.value);
			if (initialRating > 0) {
				for (let i = 0; i < initialRating; i++) {
					stars[i].classList.add('selected');
				}
			}
			stars.forEach(star => {
				star.addEventListener('click', () => {
					const value = star.getAttribute('data-value');
					ratingInput.value = value;

					stars.forEach(s => s.classList.remove('selected'));
					for (let i = 0; i < value; i++) {
						stars[i].classList.add('selected');
					}
				});
			});
		})
	</script>
</body>

</html>