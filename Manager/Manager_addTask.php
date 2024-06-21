<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/manager_header.css" />
    <link rel="stylesheet" href="./css/manager.css" />
<!--
    <script type="text/javascript">
        
        function checkOption(obj) {

            let x = obj.value;
            
            switch (x) {
            case 'auto':
                text = "Auto allocate has been selected.";
                break;
            case 'manual':
                text = "";
                break;
            default:
                text = "";
            }
            document.getElementById("disableTeam").innerText = text;
            
            var input = document.getElementById("team");
            input.disabled = obj.value == "auto";
            
            
        }
        

    </script>
-->
    <?php
        include 'db_connection.php';

        $conn = OpenCon();

    $userID = 2;

    $taskStatus = 1;
    $userStatus = 1;
    $employeeType = "Manager";
    $companyID = 21;

    $validSpecialisation = FALSE;
    $validSchedule = FALSE;
    $validDate = FALSE;

    $isManual = FALSE;

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


    // date from FORM
    if(isset($_POST['addTask'])) {

        $taskName = $_POST['taskname'];
        $taskDesc = $_POST['taskdesc'];

        $sDate = strtotime($_POST['startdate']);
        $startDate = date('Y-m-d', $sDate);
        
        $eDate = strtotime($_POST['enddate']);
        $endDate = date('Y-m-d', $eDate);

        $priority = $_POST['priority'];

        if (isset($_POST['team'])) {
            $teamID = $_POST['team'];
        }

        $specialisationIDName = $_POST['specialisationidname'];

        $specialisationIDNameE = explode(" ", $specialisationIDName);

        $specialisationIDSub = $specialisationIDNameE[0];
        
        $specialisationName = $specialisationIDNameE[1];


        // find how many and which staff with the specific specialisation
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
                echo "window.location = 'Manager_addTask.php';";
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
            

            // check if endDate is not less than startDate
            if ($endDate >= $startDate) {

                $validDate = TRUE;

                // if there are staff working on the specific date
                if ($result->num_rows > 0) {
            
                    $validSchedule = TRUE;
            
                } else {
                    echo "<script type='text/javascript'>";
                    echo "alert('There are no staff working between ".$startDate." and ".$endDate.". Please select other date.');";
                    echo "window.location = 'Manager_addTask.php';";
                    echo "</script>";
                }

            } else {
                echo "<script type='text/javascript'>";
                echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
                echo "window.location = 'Manager_addTask.php';";
                echo "</script>";
            }

        // auto allocation for PT only
        if(isset($_POST['autoallocate']) && $_POST['autoallocate'] == 'on') {

            $autoallocate = TRUE;

            if ($validSpecialisation && $validSchedule && $validDate) {
                header('location: Manager_addUsersTask.php?taskname='.$taskName.'&taskdesc='.$taskDesc.'&specialisationidname='.$specialisationIDName.'&startdate='.$startDate.'&enddate='.$endDate.'&priority='.$priority.'&autoallocate='.$autoallocate.'&numstaffteam='.$numStaffTeam.'&mainteamid='.$teamID);
            }


        // manual allocation for both FT and PT
        } else {

            $isManual = TRUE;
            
            if ($validSpecialisation && $validSchedule && $validSchedule && $validDate) {
                header('location: Manager_addUsersTask.php?taskname='.$taskName.'&taskdesc='.$taskDesc.'&specialisationidname='.$specialisationIDName.'&startdate='.$startDate.'&enddate='.$endDate.'&priority='.$priority.'&ismanual='.$isManual.'&numstaffteam='.$numStaffTeam.'&mainteamid='.$teamID);
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
                <h2>Allocate / Schedule Task</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addTask" action="Manager_addTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="taskname">Task Name</label>
                                        <input type="text" id="taskname" name="taskname" required>
                                            
                                        <label for="taskdesc">Task Description</label>
                                        <textarea id="taskdesc" name="taskdesc" rows="6" required></textarea>

                                        <label for="specialisation">Specialisation</label>
                                        <select id="specialisationidname" name="specialisationidname" required>
                                            <?php foreach ($specialisations as $specialisation):
                                                echo "<option value='". $specialisation['SpecialisationID']." ".$specialisation['SpecialisationName']."'>" . $specialisation['SpecialisationName']."</option>";
                                            endforeach; ?>
                                        </select>

                                        <label for="team">Team</label><!--<p id="disableTeam" style="color:red;"></p>-->
                                        <select id="team" name="team" required>
                                            <?php foreach ($teams as $team):
                                                echo "<option value='". $team['MainTeamID']."'>" . $team['TeamName']."</option>";
                                            endforeach; ?>
                                        </select>
                                    
                                    </div>

                                    <div class="col-50">

                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority" required>
                                            <option value="3">Low</option>
                                            <option value="2">Mid</option>
                                            <option value="1">High</option>
                                        </select>
                                        
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" required>

                                        <label for="enddate">End Date</label>
                                        <input type="date" id="enddate" name="enddate" required>

                                        <!--<label for="allocate">Manual or Auto Allocate?</label>
                                        <select id="allocate" name="allocate" oninput="checkOption(this)">
                                            <option value="manual">Manual Allocation</option>
                                            <option value="auto">Auto Allocation</option>
                                        </select>-->

                                        <label>
                                            Auto Allocate (for Part-Time) <input type="checkbox" name="autoallocate" value="on">
                                        </label>
                                    </div>
                                
                                </div>

                                <button name="addTask" type="submit" class="btn">Click to Allocate to users</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>
