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

    $taskStatus = 1;
    $userStatus = 1;

    $validSpecialisation = FALSE;
    $validSchedule = FALSE;
    $validDate = FALSE;

    $isManual = FALSE;

    // get specialisation for the select option
    // get specialisation for the select option
    $sql = "SELECT * FROM specialisation WHERE CompanyID = ".$companyID." ORDER BY SpecialisationName ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $specialisations = $result->fetch_all(MYSQLI_ASSOC);

    // get team for the select option
    $sql = "SELECT MainTeamID, TeamName FROM teaminfo"
        . " WHERE ManagerID = ".$userID." AND CompanyID = ".$companyID." ORDER BY TeamName ASC;";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $teams = $result->fetch_all(MYSQLI_ASSOC);



    if (isset($_GET['maintaskid'])) {
        $mainTaskID = $_GET['maintaskid'];
    
        // get task detail of the specific task
        $sql = "SELECT a.MainTaskID, a.SpecialisationID, e.SpecialisationName, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, f.MainTeamID, f.TeamName
                FROM taskinfo a
                INNER JOIN task b ON a.MainTaskID = b.MainTaskID
                INNER JOIN teaminfo f ON f.MainTeamID = b.MainTeamID
                INNER JOIN existinguser c ON b.UserID = c.UserID
                INNER JOIN specialisation e ON e.SpecialisationID = a.SpecialisationID
                WHERE a.MainTaskID = ".$mainTaskID."
                GROUP BY a.MainTaskID, a.SpecialisationID, e.SpecialisationName, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, f.MainTeamID, f.TeamName;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $taskDetails = $result->fetch_all(MYSQLI_ASSOC);
    }


    // date from FORM
    if(isset($_POST['editTask'])) {

        $taskName = $_POST['taskname'];
        $taskDesc = $_POST['taskdesc'];

        $sDate = strtotime($_POST['startdate']);
        $startDate = date('Y-m-d', $sDate);
        
        $eDate = strtotime($_POST['enddate']);
        $endDate = date('Y-m-d', $eDate);

        $priority = $_POST['priority'];
        $teamID = $_POST['team'];
        $statusID = $_POST['statusid'];

        $mainTaskID = $_POST['maintaskid'];
        

        $specialisationIDName = $_POST['specialisationidname'];

        $specialisationIDNameE = explode(" ", $specialisationIDName);

        $specialisationIDSub = $specialisationIDNameE[0];
        
        $specialisationName = $specialisationIDNameE[1];


        // find how many and who is in the team that the manager is managing with the specific specialisation
        $sql = "WITH abc AS (SELECT MainTeamID FROM teaminfo WHERE ManagerID = ".$userID.")"
        . " SELECT c.UserID FROM abc a"
        . " INNER JOIN team b on a.MainTeamID = b.MainTeamID"
        . " INNER JOIN existinguser c on c.UserID = b.UserID"
        . " WHERE c.SpecialisationID = ".$specialisationIDSub;

        if(isset($_POST['autoallocate']) && $_POST['autoallocate'] == 'on') {

            $sql .= " AND c.Role = 'PT' AND c.Status = ".$userStatus." AND c.CompanyID = ".$companyID.";";

        } else {

            $sql .= " AND c.Role IN ('PT','FT') AND c.Status = ".$userStatus." AND c.CompanyID = ".$companyID.";";
            
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $teamUserIDs = $result->fetch_all(MYSQLI_ASSOC);

        $numStaffTeam = count($teamUserIDs);

        // indicate that there are staff in the team with the specific specialisation
        if ($numStaffTeam > 0) {

            $validSpecialisation = TRUE;

        } else {
            echo "<script type='text/javascript'>";
            echo "alert('There are no staff with ".$specialisationName.". Please select other specialisation.');";
            echo "window.location = 'Manager_editTask.php?maintaskid=".$mainTaskID."'";
            echo "</script>";
        }


        // check if the staff from previous query is working on the dates stated
        $sql = "SELECT a.UserID, a.FirstName, IFNULL(SUM(d.Status),0) AS totalTasks FROM existinguser a"
            . " LEFT JOIN schedule b ON a.UserID = b.UserID"
            . " LEFT JOIN task c ON b.UserID = c.UserID"
            . " LEFT JOIN taskinfo d ON c.MainTaskID = d.MainTaskID"
            . " WHERE b.WorkDate BETWEEN '".$startDate."' AND '".$endDate."'";
            
        $sql .= " AND (a.UserID = ".$teamUserIDs[0]['UserID'];

        // if there are more than 1 staff
        if ($numStaffTeam > 1) {
            for ($i = 1; $i < $numStaffTeam; $i++) {
    
                $sql .= " OR a.UserID = ".$teamUserIDs[$i]['UserID'];
                    
            }
        }
                                
        $sql .= ") GROUP BY a.UserID, a.FirstName"
            ." ORDER BY totalTasks ASC;";
    
        //echo " ; SQL2 = ".$sql;
    
        $result = $conn->query($sql);
                    
        // Check for errors
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        // check if endDate is not less than startDate
        if ($endDate >= $startDate) {

            $validDate = TRUE;

            // if there are staff working on the specific date
            if ($result->num_rows > 0) {
        
                $validSchedule = TRUE;
        
            } else {
                echo "<script type='text/javascript'>";
                echo "alert('There are no staff working between ".$startDate." and ".$endDate.". Please select other date.');";
                echo "window.location = 'Manager_editTask.php?maintaskid=".$mainTaskID."'";
                echo "</script>";
            }

        } else {
            echo "<script type='text/javascript'>";
            echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
            echo "window.location = 'Manager_editTask.php?maintaskid=".$mainTaskID."'";
            echo "</script>";
        }


        if(isset($_POST['autoallocate']) && $_POST['autoallocate'] == 'on') {
            
            $autoallocate = TRUE;

            if ($validSpecialisation && $validSchedule && $validDate) {
                header('location: Manager_editUsersTask.php?taskname='.$taskName.'&taskdesc='.$taskDesc.'&specialisationidname='.$specialisationIDName.'&startdate='.$startDate.'&enddate='.$endDate.'&priority='.$priority.'&autoallocate='.$autoallocate.'&numstaffteam='.$numStaffTeam."&mainteamid=".$teamID."&maintaskid=".$mainTaskID."&statusid=".$statusID);
            }

        } else {

            $isManual = TRUE;
            
            if ($validSpecialisation && $validSchedule && $validSchedule && $validDate) {
                header('location: Manager_editUsersTask.php?taskname='.$taskName.'&taskdesc='.$taskDesc.'&specialisationidname='.$specialisationIDName.'&startdate='.$startDate.'&enddate='.$endDate.'&priority='.$priority.'&ismanual='.$isManual.'&numstaffteam='.$numStaffTeam."&mainteamid=".$teamID."&maintaskid=".$mainTaskID."&statusid=".$statusID);
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
                <h2>Edit Task</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editTask" action="Manager_editTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <?php if (isset($_GET['maintaskid'])) { ?>
                                        <input type="hidden" name="maintaskid" value="<?php echo $mainTaskID; ?>">
                                        

                                        <label for="taskname">Task Name</label>
                                        <input type="text" id="taskname" name="taskname" value="<?php foreach ($taskDetails as $taskDetail):  echo $taskDetail['TaskName']; endforeach; ?>">
                                            
                                        <label for="taskdesc">Task Description</label>
                                        <textarea id="taskdesc" name="taskdesc" rows="6"><?php foreach ($taskDetails as $taskDetail):  echo $taskDetail['TaskDesc']; endforeach; ?></textarea>

                                        
                                        <label for="specialisation">Specialisation</label>
                                        <select id="specialisationidname" name="specialisationidname" required>

                                            <?php foreach ($taskDetails as $taskDetail):
                                                echo "<option value='". $taskDetail['SpecialisationID']." ". $taskDetail['SpecialisationName']."'>-- ".$taskDetail['SpecialisationName']." --</option>";
                                            endforeach; ?>

                                            <?php foreach ($specialisations as $specialisation):
                                                echo "<option value='". $specialisation['SpecialisationID']." ".$specialisation['SpecialisationName']."'>" . $specialisation['SpecialisationName']."</option>";
                                            endforeach; ?>

                                        </select>

                                        <label for="team">Team</label>
                                        <select id="team" name="team" required>

                                            <?php foreach ($taskDetails as $taskDetail):
                                                echo "<option value='". $taskDetail['MainTeamID']."'>-- " . $taskDetail['TeamName']." --</option>";
                                            endforeach; ?>

                                            <?php foreach ($teams as $team):
                                                echo "<option value='". $team['MainTeamID']."'>" . $team['TeamName']."</option>";
                                            endforeach; ?>

                                        </select>
                                    </div>

                                    <div class="col-50">

                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority">
                                            <?php foreach ($taskDetails as $taskDetail):
                                                if ($taskDetail['Priority'] == 1) {
                                                    $priorityName = "High";
                                                } else if ($taskDetail['Priority'] == 2) {
                                                    $priorityName = "Mid";
                                                } else {
                                                    $priorityName = "Low";
                                                }
                                                echo "<option value='". $taskDetail['Priority']."'>-- " . $priorityName." --</option>";
                                            endforeach; ?>
                                            <option value="3">Low</option>
                                            <option value="2">Mid</option>
                                            <option value="1">High</option>
                                        </select>
                                        
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" value="<?php foreach ($taskDetails as $taskDetail):  echo $taskDetail['StartDate']; endforeach; ?>">

                                        <label for="enddate">End Date</label><!--<p id="disableEnddate" style="color:red;"></p>-->
                                        <input type="date" id="enddate" name="enddate" value="<?php foreach ($taskDetails as $taskDetail):  echo $taskDetail['DueDate']; endforeach; ?>">
                                        
                                        <label for="status">Status</label>
                                        <select id="status" name="statusid">
                                            <?php foreach ($taskDetails as $taskDetail):
                                                if ($taskDetail['Status'] == 1) {
                                                    $statusName = "Ongoing";
                                                } else {
                                                    $statusName = "Done";
                                                }
                                                echo "<option value='". $taskDetail['Status']."'>-- " . $statusName." --</option>";
                                            endforeach; ?>
                                            <option value="0">Done</option>
                                            <option value="1">Ongoing</option>
                                        </select>

                                        <label>
                                            Auto Allocate (for Part-Time)<input type="checkbox" name="autoallocate">
                                        </label>

                                    </div>
                                    <?php } ?>
                                
                                </div>

                                <button name="editTask" type="submit" class="btn">Click to Allocate to users</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>