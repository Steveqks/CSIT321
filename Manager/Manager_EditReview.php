<?php
	session_start();
	include 'db_connection.php';

	// Check if user is logged in
	include '../Session/session_check_user_Manager.php';

	$userID = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$firstName = $_SESSION['FirstName'];
			
	// Connect to the database
	$conn = OpenCon();

	$sql = "SELECT * FROM reviews WHERE UserID = $userID";
	
	$stmt = $conn->prepare($sql);
	//$stmt->bind_param("i", $userID);
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
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="./css/manager_header.css" />
    <link rel="stylesheet" href="./css/manager.css" />
	<style>
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
			margin-bottom: 20px;
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
    <div class="topSection">
        <img class="logo" src="./Images/tms.png" alt="TrackMySchedule Logo">
    </div>
    
    <!-- MIDDLE SECTION -->
    <div class="contentNav">

        <!-- LEFT SECTION (NAVIGATION BAR) -->
        <?php include 'navigation.php'; ?>
        
        <!-- RIGHT SECTION (REVIEW TABLE) -->
        <div class="review-section">
            <div class="review-header">
                <i class="fas fa-user"></i>
                <h2>Edit A Review</h2>
			</div>
			<div class = "review-form">
				<?php if($review): ?>
					<form action = "Manager_UpdateReview.php" method = "post">
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