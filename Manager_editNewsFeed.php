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


        if (isset($_GET['editnewsfeedid'])) {

            $newsFeedID = $_GET['editnewsfeedid'];

            $sql = "SELECT * FROM newsfeed WHERE NewsFeedID = ".$newsFeedID;
            

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $editNewsFeed = $result->fetch_all(MYSQLI_ASSOC);

        }



        if (isset($_POST['editNewsFeed'])) {

            $title = $_POST['title'];
            $desc = $_POST['desc'];
            $newsFeedID = $_POST['newsfeedid'];

            $stmt = $conn->prepare("UPDATE newsfeed SET NewsTitle=?,NewsDesc=?,DatePosted=CURRENT_TIMESTAMP WHERE NewsFeedID=?");

            $stmt->bind_param("ssi",$title,$desc,$newsFeedID);

            if ($stmt->execute()) {
                                
                echo "<script type='text/javascript'>";
                echo "alert('News Feed has been updated.');";
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
        <div class="navBar">
            <nav>
                <ul>
                    <li><a href="Manager_viewTasks.php"><?php echo "$firstName, Staff(Manager)"?></a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&manageaccount=true">Manage Account</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&taskmanagenent=true">Task Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&leavemanagenent=true">Leave Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&attendancemanagenent=true">Time/Attendance Tracking</a></li>
                    <li><a href="Manager_viewNewsFeed.php">News Feed Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&projectmanagenent=true">Project Management</a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

            
            
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
                            <form name="editNewsFeed" action="Manager_editNewsFeed.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                    <?php if (isset($_GET['editnewsfeedid'])) { ?>
                                        <input type="hidden" name="newsfeedid" value="<?php echo $newsFeedID; ?>">

                                        <label for="title">Title</label>
                                        <input type="text" id="title" name="title" value="<?php foreach ($editNewsFeed as $editPost): echo $editPost['NewsTitle']; endforeach; ?>">
                                        
                                        <label for="desc">Description</label>
                                        <textarea id="desc" name="desc" rows="6"><?php foreach ($editNewsFeed as $editPost): echo $editPost['NewsDesc']; endforeach; ?></textarea>
                                    <?php } ?>
                                    </div>
                                </div>

                                <button name="editNewsFeed" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>