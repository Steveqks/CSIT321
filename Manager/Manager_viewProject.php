<?php
    session_start();
    
    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];
    $employeeType = $_SESSION['Role'];

    // Connect to the database
    $conn = OpenCon();

    if (isset($_GET['mainprojectid'])) {

        $mainProjectID = $_GET['mainprojectid'];
    
        // get project details
        $sql = "SELECT a.MainProjectID, a.ProjectName, a.StartDate, a.EndDate FROM projectinfo a
                INNER JOIN project d ON d.MainProjectID = a.MainProjectID
                WHERE a.MainProjectID = ".$mainProjectID."
                GROUP BY a.MainProjectID, a.ProjectName, a.StartDate, a.EndDate;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projectDetails = $result->fetch_all(MYSQLI_ASSOC);


    
        // get team project details
        $sql = "SELECT a.PoolName, b.SpecialisationName FROM specialisationpoolinfo a
                INNER JOIN project d ON d.MainPoolID = a.MainPoolID
                LEFT JOIN specialisation b ON a.SpecialisationID = b.SpecialisationID
                WHERE d.MainProjectID = ".$mainProjectID."
                GROUP BY a.PoolName, b.SpecialisationName;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $teamProjectDetails = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
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
            <div class="task-header">
                <h2>View Project Details</h2>
            </div>

            <div class="innerContent">
                <div>
                    <p>Project Name: <span class="details"><?php foreach ($projectDetails as $projectDetail): echo $projectDetail['ProjectName']; endforeach; ?></span></p>

                    <p>Start Date: <span class="details"><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['StartDate'])); endforeach; ?></span></p>

                    <p>End Date: <span class="details"><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['EndDate'])); endforeach; ?></span></p>

                    <p>Specialisation(s):  <span class="details"><?php for ($i = 0; $i < count($teamProjectDetails); $i++) { ?> <?php echo "<br><br>".$i+1 .". ".$teamProjectDetails[$i]['SpecialisationName'];?><?php } ?></span></p>

                    <p>Specialisation Pool(s):  <span class="details"><?php for ($i = 0; $i < count($teamProjectDetails); $i++) { ?> <?php echo "<br><br>".$i+1 .". ".$teamProjectDetails[$i]['PoolName'];?><?php } ?></span></p>

                    <a href="Manager_editProject.php?mainprojectid=<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['MainProjectID']; endforeach; ?>" class="edit-button">Edit Project</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>