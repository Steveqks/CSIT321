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

        // get teams for the team option in form
/*        $sql = "SELECT a.MainProjectID, a.ProjectName, concat(c.FirstName, ' ', c.LastName) AS fullName FROM projectinfo a
                INNER JOIN project d ON d.MainProjectID = a.MainProjectID
                LEFT JOIN teaminfo b ON d.MainTeamID = b.MainTeamID
                LEFT JOIN existinguser c ON b.ManagerID = c.UserID
                WHERE a.ProjectManagerID = ".$userID."
                AND a.EndDate > CURRENT_TIMESTAMP
                GROUP BY a.MainProjectID, a.ProjectName;";
*/

        $sql = "SELECT *FROM projectinfo
                WHERE ProjectManagerID = ".$userID."
                AND EndDate > CURRENT_TIMESTAMP;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_GET['mainprojectid'])) {
            $mainProjectID = $_GET['mainprojectid'];

            // Delete project
            $stmt = $conn->prepare("DELETE FROM projectinfo WHERE MainProjectID = ?");

            $stmt->bind_param("i",$mainProjectID);

            if ($stmt->execute()) {

                // Delete project
                $stmt = $conn->prepare("DELETE FROM project WHERE MainProjectID = ?");
    
                $stmt->bind_param("i",$mainProjectID);

                if ($stmt->execute()) {
                    echo "<script type='text/javascript'>";
                    echo "alert('Project has been deleted.');";
                    echo "window.location = 'Manager_viewProjectList.php';";
                    echo "</script>";
                }
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
                    <h2>View Projects</h2>
            </div>

            <div class="innerContent">
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
                                <td><a href="Manager_viewProjectList.php?mainprojectid=<?php echo $project['MainProjectID']; ?>">Delete</a></td>
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
</html>