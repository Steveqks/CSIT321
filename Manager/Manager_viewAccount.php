<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/manager_header.css" />
    <link rel="stylesheet" href="./css/manager.css" />

    <?php
        session_start();
        include 'db_connection.php';

        // Check if user is logged in
        if (!isset($_SESSION['Email']))
        {
            header("Location: ../Unregistered Users/LoginPage.php");
            exit();
        }

        $userID = $_SESSION['UserID'];
        $firstName = $_SESSION['FirstName'];
        $companyID = $_SESSION['CompanyID'];
        $employeeType = $_SESSION['Role'];

        // Connect to the database
        $conn = OpenCon();

        // get teams for the team option in form
        $sql = "SELECT * FROM existinguser WHERE UserID = ".$userID;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $userDetails = $result->fetch_assoc();
        
    ?>
</head>
<body>
    <!-- Top Section -->
    <div class="topSection">
        <img class="logo" src="./Images/tms.png">
    </div>

    <!-- Middle Section -->
    <div class="contentNav">
            
        <!-- Left Section (Navigation) -->
        <?php include_once('navigation.php');?>
            
        <!-- Right Section (Activity) -->
        <div class="content">
            <div class="task-header">
                <i class="fas fa-user"></i>
                    <h2>View Account Details</h2>
            </div>

            <div class="innerContent">
                <div class="details">
                    <p>First Name: <span><?php echo $userDetails['FirstName']; ?></span></p>
                    <p>Last Name: <span><?php echo $userDetails['LastName']; ?></span></p>
                    <p>Email: <span><?php echo htmlspecialchars($userDetails['Email']); ?></span></p>
                    <a href="Manager_editAccount.php" class="edit-button">Edit Account Details</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>