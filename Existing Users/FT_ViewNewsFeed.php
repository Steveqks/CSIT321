    <?php
        session_start();
        include 'db_connection.php';

        // Check if user is logged in
        include '../Session/session_check_user_FT.php';

        $userID = $_SESSION['UserID'];
        $FirstName = $_SESSION['FirstName'];
        $companyID = $_SESSION['CompanyID'];
        $employeeType = $_SESSION['Role'];

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
		
		.content {
			border-radius: 5px;
			width: 80%;
			padding: 10px 20px 10px 20px;
		}
		.categories button {
			margin-left: 30px;
			background-color: white;
			border: 0.5px solid black;
			padding: 7px 15px;
		}
		.categories button:hover {
			background-color: rgb(229, 229, 229);
		}
		.nameDateNewsFeed {
			overflow: hidden;
			padding: 30px 0 30px 0;
		}
		.nameDateNewsFeed a, .nameDateNewsFeed span {
			float: left;
		}
		.nameDateNewsFeed a {
			margin-left: 20px;
		}
		.dateNewsFeed {
			float: right;
		}
		.newsFeedContents {
			background-color:#f2f2f2;
			border: 1px solid lightgrey;
			padding: 3%;
		}
				
	</style>
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
                <li><a href="FT_HomePage.php"><?php echo "$FirstName, Staff(FT)"?></a></li>
                <li><a href="FT_AccountDetails.php">Manage Account</a></li>
                <li><a href="FT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="FT_ViewNewsFeed.php">View News Feed</a></li>
				<li><a href="FT_ReviewManagement.php">Leave a Review!</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
            
            
        <!-- Right Section (Activity) -->
        <div class="content">
            <div class="task-header">
                <h2>View News Feed</h2>
                <div class="categories">
                    <label for="categories">View By:
                        <a href='FT_ViewNewsFeed.php?viewCompany=true'><button>Company</button></a>
                        <a href='FT_ViewNewsFeed.php?viewProject=true'><button>Project</button></a>
                    </label>
                </div>
            </div>

             <div class="innerContentNewsFeed">
                
                <?php
                
                if($viewProject) {

                    foreach ($projectNewsFeed as $project):?>

                    <div class="nameDateNewsFeed">

                        <span><?php echo $project['fullName']; ?></span>

                        <div class="dateNewsFeed">
                            <span><?php echo date('F j, Y',strtotime($project['DatePosted'])); ?></span>
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

                                <div class="dateNewsFeed">
                                    <span><?php echo date('F j, Y',strtotime($company['DatePosted'])); ?></span>
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
</html>