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


        if (isset($_POST['addNewsFeed'])) {

            $title = $_POST['title'];
            $desc = $_POST['desc'];

            $stmt = $conn->prepare("INSERT INTO newsfeed (ManagerID,NewsTitle,NewsDesc,DatePosted) VALUES (?,?,?,CURRENT_TIMESTAMP)");

            $stmt->bind_param("iss",$userID,$title,$desc);

            if ($stmt->execute()) {
                                
                echo "<script type='text/javascript'>";
                echo "alert('News Feed has been posted.');";
                echo "window.location = 'Manager_viewNewsFeed.php';";
                echo "</script>";
            }
        }
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
                    <h2>Post News Feed</h2>
            </div>

            <div class="innerContent">
            <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addNewsFeed" action="Manager_addNewsFeed.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="title">Title</label>
                                        <input type="text" id="title" name="title" required>
                                        
                                        <label for="desc">Description</label>
                                        <textarea id="desc" name="desc" rows="6" required></textarea>
                                        
                                    </div>
                                </div>

                                <button name="addNewsFeed" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>