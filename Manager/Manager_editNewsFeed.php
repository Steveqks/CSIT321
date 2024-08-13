<?php
    session_start();
    
    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];

    // Connect to the database
    $conn = OpenCon();


    if (isset($_GET['editnewsfeedid'])) {

        $newsFeedID = $_GET['editnewsfeedid'];

        $sql = "SELECT * FROM newsfeed WHERE NewsFeedID = ".$newsFeedID;
        

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $editNewsFeed = $result->fetch_all(MYSQLI_ASSOC);

        // Close the statement and connection
        $stmt->close();
        CloseCon($conn);

    }


    if (isset($_POST['editNewsFeed'])) {

        $title = $_POST['title'];
        $desc = $_POST['desc'];
        $newsFeedID = $_POST['newsfeedid'];

        $stmt = $conn->prepare("UPDATE newsfeed SET NewsTitle=?,NewsDesc=?,DatePosted=CURRENT_TIMESTAMP WHERE NewsFeedID=?");

        $stmt->bind_param("ssi",$title,$desc,$newsFeedID);

        if ($stmt->execute()) {

            // Close the statement and connection
            $stmt->close();
            CloseCon($conn);

            header("Location: Manager_editNewsFeed.php?editnewsfeedid=".$newsFeedID."&message=News Feed has been updated.");
            exit();

        } else {

            // Close the statement and connection
            $stmt->close();
            CloseCon($conn);

            header("Location: Manager_editNewsFeed.php?editnewsfeedid=".$newsFeedID."&error=Error updating news feed details.");
            exit();
        }
    }
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
            <h2>Edit News Feed</h2>

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
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

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