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
    // Collect and sanitize form inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    // Check if passwords match
    if ($password != $confirm_password) {
        header("Location: PT_EditAccountDetails.php?error=Passwords do not match. Please try again.");
        exit();
    }

    // Prepare and execute the update statement
    $sql = "UPDATE existinguser SET FirstName = ?, LastName = ?, Email = ?, Password = ? WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $user_id);
    
    if ($stmt->execute()) {
        header("Location: PT_EditAccountDetails.php?message=Account details updated successfully.");
        exit();
    } else {
        header("Location: PT_EditAccountDetails.php?error=Error updating account details.");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
}
?>
