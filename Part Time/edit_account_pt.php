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

// Fetch user details
$sql = "SELECT FirstName, LastName, Email, Password FROM existinguser WHERE UserID = 1"; // Assuming UserID is 1 for demonstration
$stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the database connection
$stmt->close();
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account Details (PT)</title>
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
            height: 95vh;
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

        .edit-section {
            padding: 20px;
            flex-grow: 1;
        }

        .edit-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .edit-header i {
            margin-right: 10px;
        }

        .edit-header h2 {
            margin: 0;
        }

        .edit-form {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            display: flex;
            flex-direction: column;
        }

        .edit-form .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .edit-form label {
            margin-bottom: 5px;
        }

        .edit-form input, .edit-form select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%;
            margin-bottom: 15px;
        }

        .form-half {
            display: inline-flex;
            justify-content: space-between;
        }

        .form-half .form-group {
            flex: 0 0 48%;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 20px;
        }

        .edit-button, .cancel-button {
            padding: 10px 20px;
            font-weight: bold;
            font-size: 1em;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            border-radius: 5px;
            border: 1px solid black;
            margin-left: 10px;
        }

        .edit-button {
            background-color: #28a745;
            color: white;
        }

        .edit-button:hover {
            background-color: #218838;
            color: white;
        }

        .cancel-button {
            background-color: white;
            color: black;
        }

        .cancel-button:hover {
            background-color: #ddd;
            color: black;
        }

        .error-message {
            color: red;
            margin-top: -10px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
    </style>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var errorMessage = document.getElementById("error-message");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match. Please try again.";
                return false;
            } else {
                errorMessage.textContent = "";
                return true;
            }
        }
    </script>
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
                <li><a href="#">name, Staff (PT)</a></li>
                <li><a href="#">Manage Account</a></li>
                <li><a href="#">Attendance Management</a></li>
                <li><a href="#">Leave Management</a></li>
                <li><a href="#">Time Management</a></li>
                <li><a href="#">View News Feed</a></li>
                <li><a href="#">Swap Shifts</a></li>
                <li><a href="#">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- RIGHT SECTION (EDIT FORM) -->
        <div class="edit-section">
            <div class="edit-header">
                <i class="fas fa-user-edit"></i>
                <h2>Edit Account Details</h2>
            </div>
            <form action="update_account.php" method="post" class="edit-form" onsubmit="return validateForm()">
                <div class="form-half">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required>

                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['LastName']); ?>" required>

                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user['Password']); ?>" required>

                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div id="error-message" class="error-message"></div>
                <div class="button-group">
                    <a href="acc_details_pt.php" class="cancel-button">Cancel</a>
                    <button type="submit" class="edit-button">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
