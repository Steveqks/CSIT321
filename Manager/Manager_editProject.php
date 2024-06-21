<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/manager_header.css" />
    <link rel="stylesheet" href="./css/manager.css" />

    <?php
        include 'db_connection.php';

        $conn = OpenCon();

        $employeeType = "Manager";
        $userID = 2;
        $companyID = 21;

        if (isset($_GET['mainprojectid'])) {
            $mainProjectID = $_GET['mainprojectid'];

            // get the project's details - ProjectID, ProjectName, TeamID, TeamName, StartDate, EndDate
            $sql = "SELECT a.MainProjectID, a.ProjectName, c.MainTeamID, b.TeamName, a.StartDate, a.EndDate
                    FROM projectinfo a
                    INNER JOIN project c ON a.MainProjectID = c.MainProjectID
                    INNER JOIN teaminfo b ON c.MainTeamID = b.MainTeamID
                    WHERE a.MainProjectID = ".$mainProjectID;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $projectDetails = $result->fetch_all(MYSQLI_ASSOC);
        }


        // get teams for the team option in form
        $sql = "SELECT MainTeamID, TeamName FROM teaminfo WHERE ManagerID = ".$userID;

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $teams = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_POST['editProject'])) {

            if ($_POST['startdate'] < $_POST['enddate']) {

                $projectName = $_POST['projectname'];
                $mainTeamID = $_POST['mainteamid'];
                $startDate = $_POST['startdate'];
                $endDate = $_POST['enddate'];
                $mainProjectID = $_POST['mainprojectid'];

                $stmt = $conn->prepare("UPDATE projectinfo SET ProjectManagerID=?,CompanyID=?,ProjectName=?,StartDate=?,EndDate=? WHERE MainProjectID=?");

                $stmt->bind_param("iisssi",$userID,$companyID,$projectName,$startDate,$endDate,$mainProjectID);

                if ($stmt->execute()) {

                    $stmt = $conn->prepare("DELETE FROM project WHERE MainProjectID = ?");

                    $stmt->bind_param("i",$mainProjectID);

                    if ($stmt->execute()) {

                        $stmt = $conn->prepare("INSERT INTO project (MainProjectID,MainTeamID) VALUES (?,?)");

                        foreach ($teams as $team) {

                            $stmt->bind_param("ii",$mainProjectID,$mainTeamID);

                            $stmt->execute();

                        }

                        echo "<script type='text/javascript'>";
                        echo "alert('Project has been updated successfully.');";
                        echo "window.location = 'Manager_viewProject.php';";
                        echo "</script>";
                    }
                }
            } else {
                echo "<script type='text/javascript'>";
                echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
                echo "window.location = 'Manager_editProject.php?mainprojectid=".$mainProjectID."';";
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
                    <?php if ($employeeType == "Manager") { ?>
                        <li><a> &lt;name&gt;, Manager</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&manageaccount=true">Manage Account</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&taskmanagenent=true">Task Management</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&leavemanagenent=true">Leave Management</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&attendancemanagenent=true">Time/Attendance Tracking</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&newsfeedmanagenent=true">News Feed Management</a></li>
                        <li><a href="Manager_allHeadings.php?employeetype=Manager&projectmanagenent=true">Project Management</a></li>
                        <li><a href="#">Logout</a></li>
                        <?php } ?>
                </ul>
            </nav>
        </div>

            
            
        <!-- Right Section (Activity) -->
        <div class="content">
            <div class="task-header">
                <i class="fas fa-user"></i>
                    <h2>Edit Project</h2>
            </div>

            <div class="innerContent">
            <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editProject" action="Manager_editProject.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                    <?php if (isset($_GET['mainprojectid'])) { ?>
                                        <input type="hidden" name="mainprojectid" value="<?php echo $mainProjectID; ?>">

                                        <label for="projectname">Project Name</label>
                                        <input type="text" id="projectname" name="projectname" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['ProjectName']; endforeach; ?>">

                                        <label for="mainteamid">Team</label>
                                        <select id="mainteamid" name="mainteamid">

                                            <?php foreach ($projectDetails as $projectDetail):
                                                echo "<option value='". $projectDetail['MainTeamID']."'>-- " . $projectDetail['TeamName']." --</option>";
                                            endforeach; ?>

                                            <?php foreach ($teams as $team):
                                                echo "<option value='". $team['MainTeamID']."'>" . $team['TeamName']."</option>";
                                            endforeach; ?>
                                            
                                        </select>
                                            
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['StartDate']; endforeach; ?>">

                                        <label for="enddate">End Date</label>
                                        <input type="date" id="enddate" name="enddate" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['EndDate']; endforeach; ?>">
                                        
                                </div>

                                <button name="editProject" type="submit" class="btn">Update</button>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>