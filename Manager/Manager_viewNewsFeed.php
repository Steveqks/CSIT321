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

        $viewCompany = FALSE;
        $viewTeam = TRUE;

        if(isset($_GET['viewCompany'])) {

            $viewCompany = TRUE;
            $viewTeam = FALSE;

            $sql = "SELECT a.ManagerID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.NewsFeedID, a.NewsTitle, a.NewsDesc, a.DatePosted FROM newsfeed a
                    INNER JOIN existinguser b ON a.ManagerID = b.UserID
                    WHERE b.CompanyID = ".$companyID."
                    ORDER BY a.DatePosted DESC;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $companyNewsFeed = $result->fetch_all(MYSQLI_ASSOC);

        } else if ($viewTeam) {

            $sql = "SELECT CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.NewsFeedID, a.NewsTitle, a.NewsDesc, a.DatePosted FROM newsfeed a
                    INNER JOIN existinguser b ON a.ManagerID = b.UserID
                    WHERE a.ManagerID = ".$userID."
                    ORDER BY a.DatePosted DESC;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $teamNewsFeed = $result->fetch_all(MYSQLI_ASSOC);
        }

        if (isset($_GET['newsfeedid'])) {

            $newsFeedID = $_GET['newsfeedid'];

            // Delete project
            $stmt = $conn->prepare("DELETE FROM newsfeed WHERE NewsFeedID = ?");

            $stmt->bind_param("i",$newsFeedID);

            if ($stmt->execute()) {
                echo "<script type='text/javascript'>";
                echo "alert('News Feed has been deleted.');";
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
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&newsfeedmanagenent=true">News Feed Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&projectmanagenent=true">Project Management</a></li>
                    <li><a href="Logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

            
            
        <!-- Right Section (Activity) -->
        <div class="content">
            <div class="task-header">
                <h2>View News Feed</h2>
                <a href="Manager_addNewsFeed.php"><h4>Add News Feed</h4></a>
                <div class="categories">
                    <label for="categories">View By:
                        <a href='Manager_viewNewsFeed?viewCompany=true'><button>Company</button></a>
                        <a href='Manager_viewNewsFeed?viewTeam=true'><button>Team</button></a>
                    </label>
                </div>
            </div>

            <div class="innerContentNewsFeed">
                
                <?php if($viewTeam) {

                    foreach ($teamNewsFeed as $team):?>

                    <div class="nameDateNewsFeed">

                        <div class="teamNameNewsFeed">
                            <?php echo $team['fullName']; ?>
                        </div>

                        <div class="teamDateNewsFeed">
                            <?php echo date('F j, Y',strtotime($team['DatePosted'])); ?>
                            <a href="Manager_viewNewsFeed.php?newsfeedid=<?php echo $team['NewsFeedID']; ?>">Delete Post</a>
                        </div>

                    </div>
                    <div class="newsFeedContents">

                        <label for="title" style="font-weight: bold;"><?php echo $team['NewsTitle']; ?></label>
                        <p><?php echo $team['NewsDesc']; ?></p>

                    </div>
                <?php
                    endforeach;

                } else if($viewCompany) {

                    foreach ($companyNewsFeed as $company):?>

                        <div class="nameDateNewsFeed">
                            
                            <div class="companyNameNewsFeed">
                                <?php echo $company['fullName']; ?>
                            </div>

                            
                            <?php if($company['ManagerID'] == $userID) { ?>
                                <div class="teamDateNewsFeed">
                                    <?php echo date('F j, Y',strtotime($company['DatePosted'])); ?>
                                    <a href="Manager_viewNewsFeed.php?newsfeedid=<?php echo $company['NewsFeedID']; ?>">Delete Post</a>
                                </div>
                            <?php } else { ?>
                                <div class="companyDateNewsFeed">
                                    <?php echo date('F j, Y',strtotime($company['DatePosted'])); ?>
                                </div>
                            <?php } ?>

                        </div>
                        <div class="newsFeedContents">

                            <label for="title" style="font-weight: bold;"><?php echo $company['NewsTitle']; ?></label>
                            <p><?php echo $company['NewsDesc']; ?></p>

                        </div>
                    <?php
                        endforeach;
                    } ?>
            </div>
        </div>
    </div>
</body>
</html>