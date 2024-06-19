<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// $user_id = $_SESSION['user_id'];

// Connect to the database
$conn = OpenCon();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);
    $specialisation = htmlspecialchars($_POST['specialisation']);

    // Check if passwords match
    if ($password != $confirm_password) {
        header("Location: edit_account_pt.php?error=Passwords do not match. Please try again.");
        exit();
    }

    // Prepare and execute the update statement
    $sql = "UPDATE existinguser SET FirstName = ?, LastName = ?, Email = ?, Password = ?, SpecialisationID = ? WHERE UserID = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $specialisation);
    
    if ($stmt->execute()) {
        header("Location: acc_details_pt.php?message=Account details updated successfully.");
        exit();
    } else {
        header("Location: edit_account_pt.php?error=Error updating account details.");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
}
?>
