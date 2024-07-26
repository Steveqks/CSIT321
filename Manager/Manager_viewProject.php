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

    if (isset($_GET['deletemainprojectid'])) {

        $mainProjectID = $_GET['deletemainprojectid'];

        // Check if the number of Tasks related to Project
        $sql = "SELECT a.MainProjectID, (SELECT COUNT(IFNULL(b.MainTaskID,0)) AS noOfTasks FROM taskinfo b WHERE b.MainProjectID = a.MainProjectID) AS noOfTasks
                FROM project a
                WHERE a.MainProjectID = ".$mainProjectID;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $taskExist = $result->fetch_assoc();


        if ($taskExist > 0) {

            $stmt = $conn->prepare("DELETE FROM task WHERE MainTaskID = ?");

            $stmt->bind_param("i",$mainTaskID);

            if ($stmt->execute()) {

                // Delete project
                $stmt = $conn->prepare("DELETE FROM taskinfo WHERE MainTaskID = ?");
    
                $stmt->bind_param("i",$mainTaskID);

                $stmt->execute();
            }
        }


        // Delete project
        $stmt = $conn->prepare("DELETE FROM projectinfo WHERE MainProjectID = ?");

        $stmt->bind_param("i",$mainProjectID);

        if ($stmt->execute()) {

            // Delete project
            $stmt = $conn->prepare("DELETE FROM project WHERE MainProjectID = ?");

            $stmt->bind_param("i",$mainProjectID);

            if ($stmt->execute()) {

                $sql = "SELECT ProjectName FROM projectinfo WHERE MainProjectID = ".$mainProjectID;
        
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $projectName = $result->fetch_assoc();

                // Close the statement and connection
                $stmt->close();
                CloseCon($conn);

                header("Location: Manager_viewProjectList.php?message=Project ".$projectName['ProjectName']." has been deleted.");
                exit();
            }
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
            <div class="task-header">
                <h2>View Project Details</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <div class="row">
                                <div class="col-50">

                                    <span class="details">Project Name</span>
                                    <p><?php foreach ($projectDetails as $projectDetail): echo $projectDetail['ProjectName']; endforeach; ?></p>
                                        
                                    <span class="details">Start Date</span>
                                    <p><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['StartDate'])); endforeach ?></p>

                                    <span class="details">End Date</span>
                                    <p><?php foreach ($projectDetails as $projectDetail): echo date('F j, Y',strtotime($projectDetail['EndDate'])); endforeach; ?></p>

                                </div>

                                <div class="col-50">

                                    <span class="details">Specialisation(s)</span>
                                    <p><?php
                                        for ($i = 0; $i < count($teamProjectDetails); $i++) {
                                            echo $i+1 .". ".$teamProjectDetails[$i]['SpecialisationName'];
                                        }
                                    ?></p>
                                        
                                    <span class="details">Specialisation Pool(s)</span>
                                    <p><?php
                                        for ($i = 0; $i < count($teamProjectDetails); $i++) {
                                            echo $i+1 .". ".$teamProjectDetails[$i]['PoolName'];
                                        }
                                    ?></p>

                                </div>

                                <div class="col-50">
                                    <button name="deleteProject" class="delbtn" onclick="confirmDelete()">Delete</button>
                                </div>

                                <div class="col-50">
                                    <a href="Manager_editProject.php?mainprojectid=<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['MainProjectID']; endforeach; ?>" ><button name="editProject" class="btn">Edit</button></a>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">
    function confirmDelete() {

        let text = "All tasks related to this project will be deleted.\nConfirm to delete?";
        
        if (confirm(text) == true) {
            window.location = "Manager_viewProject.php?deletemainprojectid=<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['MainProjectID']; endforeach; ?>";
        } else {
            window.location = "Manager_viewProject.php?mainprojectid=<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['MainProjectID']; endforeach; ?>";
        }
    }
</script>
</html>