<?php
    session_start();
    
    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];

    // Connect to the database
    $conn = OpenCon();

    $viewCompany = FALSE;
    $viewProject = TRUE;

    if(isset($_GET['viewCompany'])) {

        $sql = "SELECT a.ManagerID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.NewsFeedID, a.NewsTitle, a.NewsDesc, a.DatePosted FROM newsfeed a
                INNER JOIN existinguser b ON a.ManagerID = b.UserID
                WHERE b.CompanyID = ".$companyID."
                ORDER BY a.DatePosted DESC;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $companyNewsFeed = $result->fetch_all(MYSQLI_ASSOC);

        if ($result->num_rows > 0) {

            $viewCompany = TRUE;
            $viewProject = FALSE;

        } else {

            $viewCompany = FALSE;
            $viewProject = FALSE;

        }

    } else if ($viewProject) {

        $sql = "SELECT CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.NewsFeedID, a.NewsTitle, a.NewsDesc, a.DatePosted FROM newsfeed a
                INNER JOIN existinguser b ON a.ManagerID = b.UserID
                INNER JOIN projectinfo c ON a.ManagerID = c.ProjectManagerID
                WHERE a.ManagerID = ".$userID."
                ORDER BY a.DatePosted DESC;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projectNewsFeed = $result->fetch_all(MYSQLI_ASSOC);

        if ($result->num_rows > 0) {

            $viewCompany = FALSE;
            $viewProject = TRUE;

        } else {

            $viewCompany = FALSE;
            $viewProject = FALSE;

        }
    }

    if (isset($_GET['deletenewsfeedid'])) {

        $newsFeedID = $_GET['deletenewsfeedid'];

        // Delete project
        $stmt = $conn->prepare("DELETE FROM newsfeed WHERE NewsFeedID = ?");

        $stmt->bind_param("i",$newsFeedID);

        if ($stmt->execute()) {
            header('Location: Manager_viewNewsFeed.php');
            exit;
        }
    }

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/manager_header.css" />
    <link rel="stylesheet" href="./css/manager.css" />
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
            <h2>View News Feed</h2>
            <a href="Manager_addNewsFeed.php"><h4>Add News Feed</h4></a>
            <div class="categories">
                <label for="categories">View By:
                    <a href='Manager_viewNewsFeed?viewCompany=true'><button>Company</button></a>
                    <a href='Manager_viewNewsFeed?viewProject=true'><button>Project</button></a>
                </label>
            </div>

            <div class="innerContentNewsFeed">
                
                <?php
                
                if($viewProject) {

                    foreach ($projectNewsFeed as $project):?>

                    <div class="nameDateNewsFeed">

                        <span><?php echo $project['fullName']; ?></span>
                        <a href="Manager_editNewsFeed.php?editnewsfeedid=<?php echo $project['NewsFeedID']; ?>">Edit Post</a>

                        <div class="dateNewsFeed">
                            <span><?php echo date('F j, Y',strtotime($project['DatePosted'])); ?></span>
                            <a href="#" onclick="return confirmDelete(<?php echo $project['NewsFeedID']; ?>)">Delete Post</a>
                        </div>

                    </div>
                    <div class="newsFeedContents">

                        <label for="title" style="font-weight: bold;"><?php echo $project['NewsTitle']; ?></label>
                        <p><?php echo $project['NewsDesc']; ?></p>

                    </div>
                <?php
                    endforeach;

                } else if($viewCompany) {

                    foreach ($companyNewsFeed as $company):?>

                        <div class="nameDateNewsFeed">

                            <?php if($company['ManagerID'] == $userID) { ?>

                                <span><?php echo $company['fullName']; ?></span>
                                <a href="Manager_editNewsFeed.php?editnewsfeedid=<?php echo $company['NewsFeedID']; ?>">Edit Post</a>

                                <div class="dateNewsFeed">

                                    <span><?php echo date('F j, Y',strtotime($company['DatePosted'])); ?></span>
                                    <a href="Manager_viewNewsFeed.php?deletenewsfeedid=<?php echo $company['NewsFeedID']; ?>">Delete Post</a>
                                
                                </div>

                            <?php } else { ?>

                                <span><?php echo $company['fullName']; ?></span>

                                <div class="dateNewsFeed">

                                    <span><?php echo date('F j, Y',strtotime($company['DatePosted'])); ?></span>
                                
                                </div>

                            <?php } ?>

                        </div>
                        <div class="newsFeedContents">

                            <label for="title" style="font-weight: bold;"><?php echo $company['NewsTitle']; ?></label>
                            <p><?php echo $company['NewsDesc']; ?></p>

                        </div>
                    <?php
                    endforeach;
                } else {
                    echo "<h4>No news feed.</h4>";
                } ?>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">
    function confirmDelete(newsFeedID) {

        let text = "Confirm to delete post?";
        
        if (confirm(text) == true) {
            window.location = "Manager_viewNewsFeed.php?deletenewsfeedid=" + newsFeedID;
        } else {
            window.location = "Manager_viewNewsFeed.php";
        }
    }
</script>
</html>