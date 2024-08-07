<?php
	session_start();
	include 'db_connection.php';
	include '../Session/session_check_user_PT.php';

	$user_id = $_SESSION['UserID'];
	$Email = $_SESSION['Email'];
	$FirstName = $_SESSION['FirstName'];

	// Connect to the database
	$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//store form variables
	$reviewTitle    = htmlspecialchars($_POST['reviewtitle']);
	$reviewrating   = htmlspecialchars($_POST['rating']);
	$reviewcomments = htmlspecialchars($_POST['reviewcomments']);
	$dateposted     =  htmlspecialchars(date('Y-m-d'));


    // Prepare and execute the update statement
    $sql = "UPDATE reviews SET ReviewTitle = ?, Rating = ?, Comments = ?, DatePosted = ? WHERE UserID = ?";
	$stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $reviewTitle, $reviewrating, $reviewcomments, $dateposted, $user_id);
	
    if ($stmt->execute()) {
        header("Location: PT_EditReview.php?message=Successfully updated your review!.");
        exit();
    } else {
        header("Location: PT_EditReview.php?error=Error updating your review!.");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
}
?>
