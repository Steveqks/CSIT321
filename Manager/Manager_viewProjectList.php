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


    $sql = "SELECT * FROM projectinfo
            WHERE ProjectManagerID = ".$userID."
            AND EndDate > CURRENT_TIMESTAMP
            ORDER BY EndDate ASC;";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    if (isset($_GET['mainprojectid'])) {
        $mainProjectID = $_GET['mainprojectid'];

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
                <h2>View Projects</h2>
            </div>

            <div class="innerContent">
                                
                <?php
                    if (isset($_GET['message'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                    } elseif (isset($_GET['error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                ?>

                <table class="tasks">

                    <tr>
                        <th>Project Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Delete</th>
                    </tr>

                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <a href="Manager_viewProject.php?mainprojectid=<?php echo $project['MainProjectID']; ?>"><?php echo $project['ProjectName']; ?></a>
                                </td>
                                <td><?php echo date('F j, Y',strtotime($project['StartDate'])); ?></td>
                                <td><?php echo date('F j, Y',strtotime($project['EndDate'])); ?></td>
                                <td><a href="#" onclick="confirmDelete()">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No projects created.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">
    function confirmDelete() {

        let text = "All tasks related to this project will be deleted.\nConfirm to delete?";
        
        if (confirm(text) == true) {
            window.location = "Manager_viewProjectList.php?mainprojectid=<?php echo $project['MainProjectID']; ?>";
        } else {
            window.location = "Manager_viewProjectList.php";
        }
    }
</script>
</html>