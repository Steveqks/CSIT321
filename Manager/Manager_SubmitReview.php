<?php
	session_start();
	include 'db_connection.php';
	include '../Session/session_check_user_Manager.php';

	$userID = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$firstName = $_SESSION['FirstName'];
			
	// Connect to the database
	$conn = OpenCon();

	if(isset($_POST['submit']))
	{
		//store form variables
		$userID = $_SESSION['UserID'];
		$reviewTitle = $_POST['reviewtitle'];
		$reviewrating = $_POST['rating'];
		$reviewcomments = $_POST['reviewcomments'];
		$dateposted = date('Y-m-d');
		
		$sql = "INSERT INTO reviews(UserID,ReviewTitle,Rating,Comments,DatePosted) VALUES ('$userID','$reviewTitle','$reviewrating','$reviewcomments','$dateposted')";
		
		//Ensure user can only submit 1 review
		$dupechecksql = "SELECT * FROM reviews WHERE USERID = $userID";
		$dupecheckQuery = mysqli_query($conn, $dupechecksql);
		
		if(mysqli_num_rows($dupecheckQuery) >= 1)
		{
			echo "<div class='message'>
				<p>1 Review already submitted, please edit your existing review!</p>
			</div> <br>";
		}
		
		else
		{
			mysqli_query($conn,$sql)or die("Error Occured");
		
			echo "<div class='message'>
				<p>Review Successfully submitted!</p>
			</div> <br>";
		}
	}
	
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
                <h2>Submit A Review</h2>
			</div>
			<div class = "review-form">
				<form action = "" method = "post">
					<label for "reviewtitle">Title: </label><br>
					<input id = "reviewtitle" name = "reviewtitle" type = "text"><br><br>
					
					<label for "rating">Rating: </label>
					<div class="rating">
						<span class="star" data-value="1">&#9733;</span>
						<span class="star" data-value="2">&#9733;</span>
						<span class="star" data-value="3">&#9733;</span>
						<span class="star" data-value="4">&#9733;</span>
						<span class="star" data-value="5">&#9733;</span>
					</div>
					
					<input type="hidden" name="rating" id="rating" required>
					
					<label for "reviewcomments">Comments: </label><br>
					<input id = "reviewcomments" name = "reviewcomments" type = "textarea"><br><br>
					<button id = "submitBtn" name = "submit">Submit Review</button>
				</form>
            </div>
        </div>
    </div>
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const stars = document.querySelectorAll('.star');
			const ratingInput = document.getElementById('rating');

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