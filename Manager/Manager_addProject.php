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

        // get team for the select option
        $sql = "SELECT MainTeamID, TeamName FROM teaminfo"
            . " WHERE ManagerID = ".$userID." AND CompanyID = ".$companyID." ORDER BY TeamName ASC;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $teams = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_POST['addProject'])) {

            if ($_POST['startdate'] < $_POST['enddate']) {

                $projectName = $_POST['projectname'];
                $teamID = $_POST['teams'];
                $startDate = $_POST['startdate'];
                $endDate = $_POST['enddate'];

                $sql = "SELECT MainProjectID FROM projectinfo
                        WHERE ProjectName LIKE '%".$projectName."%'
                        AND ProjectManagerID = ".$userID;

                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    echo "<script type='text/javascript'>";
                    echo "alert('Project Name with ".$projectName." already exist.');";
                    echo "window.location = 'Manager_addProject.php';";
                    echo "</script>";

                } else {
                    $stmt = $conn->prepare("INSERT INTO projectinfo (ProjectManagerID,CompanyID,ProjectName,StartDate,EndDate) VALUES (?,?,?,?,?)");

                    $stmt->bind_param("iisss",$userID,$companyID,$projectName,$startDate,$endDate);

                    if ($stmt->execute()) {

                        $newMainProjectID = $stmt->insert_id;

                        $stmt = $conn->prepare("INSERT INTO project (MainProjectID,MainTeamID) VALUES (?,?)");

                        foreach ($teams as $team) {

                            $stmt->bind_param("ii",$newMainProjectID,$team['MainTeamID']);

                            $stmt->execute();
                                
                            echo "<script type='text/javascript'>";
                            echo "alert('Project has been created successfully.');";
                            echo "window.location = 'Manager_viewProject.php';";
                            echo "</script>";
                        }
                    }

                }

            } else {
                echo "<script type='text/javascript'>";
                echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
                echo "window.location = 'Manager_addProject.php';";
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
                    <li><?php echo "$firstName, Staff(Manager)"?></li>
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
                <i class="fas fa-user"></i>
                    <h2>Add Project</h2>
            </div>

            <div class="innerContent">
            <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addProject" action="Manager_addProject.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="projectname">Project Name</label>
                                        <input type="text" id="projectname" name="projectname" required>

                                        <label for="teamid">Team</label>
                                        <div class="checkbox-container">
                                            <?php
                                            foreach ($teams as $team):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='teams[]' values='". $team['MainTeamID']."'>". $team['TeamName']."</div>";
                                            endforeach;
                                            ?>
                                        </div>
                                            
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" required>

                                        <label for="enddate">End Date</label>
                                        <input type="date" id="enddate" name="enddate" required>
                                    </div>
                                </div>

                                <button name="addProject" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>