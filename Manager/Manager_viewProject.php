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

        if (isset($_GET['mainprojectid'])) {

            $mainProjectID = $_GET['mainprojectid'];
        
            // get project details
            $sql = "SELECT a.MainProjectID, a.ProjectName, a.StartDate, a.EndDate, concat(c.FirstName, ' ', c.LastName) AS fullName FROM projectinfo a
                    INNER JOIN project d ON d.MainProjectID = a.MainProjectID
                    LEFT JOIN teaminfo b ON d.MainTeamID = b.MainTeamID
                    LEFT JOIN existinguser c ON b.ManagerID = c.UserID
                    WHERE a.MainProjectID = ".$mainProjectID."
                    GROUP BY a.MainProjectID, a.ProjectName, a.StartDate, a.EndDate;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $projectDetails = $result->fetch_all(MYSQLI_ASSOC);


        
            // get team project details
            $sql = "SELECT a.TeamName FROM teaminfo a
                    INNER JOIN project d ON d.MainTeamID = a.MainTeamID
                    WHERE d.MainProjectID = ".$mainProjectID.";";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $teamProjectDetails = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
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
                    <h2>View Project Details</h2>
            </div>

            <div class="innerContent">
                <div class="details">
                    <p>Project Name: <span><?php foreach ($projectDetails as $projectDetail): echo $projectDetail['ProjectName']; endforeach; ?></span></p>

                    <p>Project Manager's Name: <span><?php foreach ($projectDetails as $projectDetail): echo $projectDetail['fullName']; endforeach; ?></span></p>

                    <p>Start Date: <span><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['StartDate'])); endforeach; ?></span></p>

                    <p>End Date: <span><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['EndDate'])); endforeach; ?></span></p>

                    <p>Team(s): <?php foreach ($teamProjectDetails as $teamProjectDetail): ?> <span> <?php echo $teamProjectDetail['TeamName'];?></span><?php endforeach; ?></p>

                    <a href="Manager_editProject.php?mainprojectid=<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['MainProjectID']; endforeach; ?>" class="edit-button">Edit Project</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>