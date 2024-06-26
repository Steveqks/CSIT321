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

            // get the project's details - ProjectID, ProjectName, TeamID, TeamName, StartDate, EndDate
            $sql = "SELECT MainProjectID, ProjectName, StartDate, EndDate
                    FROM projectinfo
                    WHERE MainProjectID = ".$mainProjectID;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $projectDetails = $result->fetch_all(MYSQLI_ASSOC);
        
            // get teams of the project
            $sql = "SELECT a.MainTeamID, b.TeamName FROM project a
                    INNER JOIN teaminfo b ON a.MainTeamID = b.MainTeamID
                    WHERE a.MainProjectID = ".$mainProjectID;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $projectTeams = $result->fetch_all(MYSQLI_ASSOC);
        
            // get teams for the team option in form
            $sql = "SELECT MainTeamID, TeamName FROM teaminfo
                    WHERE MainTeamID NOT IN (".$projectTeams[0]['MainTeamID'];

            if (count($projectTeams) > 1) {
                for ($i = 1; $i < count($projectTeams); $i++) {
                    $sql .= ", ".$projectTeams[$i]['MainTeamID'];
                }
            }

            $sql .= ");";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $teams = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
        }

        if (isset($_POST['editProject'])) {

            if ($_POST['startdate'] < $_POST['enddate']) {

                $projectName = $_POST['projectname'];
                $mainTeamID = $_POST['teams'];
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

                        foreach ($mainTeamID as $team) {

                            $stmt->bind_param("ii",$mainProjectID,$team);

                            $stmt->execute();

                        }

                        echo "<script type='text/javascript'>";
                        echo "alert('Project has been updated successfully.');";
                        echo "window.location = 'Manager_viewProjectList.php';";
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
        <?php include_once('navigation.php');?>
            
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

                                        <label for="teams">Team</label>
                                        <div class="checkbox-container">
                                            <?php
                                            foreach ($projectTeams as $projectTeam):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='teams[]' value='". $projectTeam['MainTeamID']."' checked>". $projectTeam['TeamName']."</div>";
                                            endforeach;

                                            foreach ($teams as $team):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='teams[]' value='". $team['MainTeamID']."'>". $team['TeamName']."</div>";
                                            endforeach;
                                            ?>
                                        </div>
                                            
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