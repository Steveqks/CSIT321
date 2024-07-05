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


            // get PT staff that is working on the specific dates
            $sql = "WITH abc AS (SELECT MainTeamID FROM teaminfo WHERE ManagerID = ".$userID.")"
                . " SELECT c.UserID FROM abc a"
                . " INNER JOIN team b on a.MainTeamID = b.MainTeamID"
                . " INNER JOIN existinguser c on c.UserID = b.UserID"
                . " INNER JOIN schedule d ON c.UserID = d.UserID"
                . " WHERE c.SpecialisationID = ".$specialisationIDSub
                . " AND c.Status = ".$userStatus
                . " AND b.MainTeamID = ".$teamID
                . " AND c.Role = 'PT'"
                . " AND d.WorkDate >= '".$startDate."' AND d.WorkDate <= '".$endDate."'"
                . " GROUP BY c.UserID;";

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $PTUsers = $result->fetch_all(MYSQLI_ASSOC);

            //echo "SQL1 ;; ".$sql;

            // FT Users
            $sql = "WITH abc AS (SELECT MainTeamID FROM teaminfo WHERE ManagerID = ".$userID.")"
                . " SELECT c.UserID FROM abc a"
                . " INNER JOIN team b on a.MainTeamID = b.MainTeamID"
                . " INNER JOIN existinguser c on c.UserID = b.UserID"
                . " WHERE c.SpecialisationID = ".$specialisationIDSub
                . " AND c.Status = ".$userStatus
                . " AND b.MainTeamID = ".$teamID
                . " AND c.Role = 'FT'"
                . " GROUP BY c.UserID;";

            //echo "<br> SQL2 ;; ".$sql;

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $FTUsers = $result->fetch_all(MYSQLI_ASSOC);



            // find how many and which staff with the specific specialisation (FT & PT)
            // to put into NumStaff column in taskinfo table
            $sql = "WITH abc AS (SELECT MainTeamID FROM teaminfo WHERE ManagerID = ".$userID.")"
                . " SELECT c.UserID FROM abc a"
                . " INNER JOIN team b on a.MainTeamID = b.MainTeamID"
                . " INNER JOIN existinguser c on c.UserID = b.UserID"
                . " WHERE c.SpecialisationID = ".$specialisationIDSub
                . " AND c.Role IN ('PT','FT') AND c.Status = ".$userStatus." AND c.CompanyID = ".$companyID
                . " GROUP BY c.UserID;";

            //echo "<br> SQL3 ;; ".$sql;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $teamUserIDs = $result->fetch_all(MYSQLI_ASSOC);

            $numStaffTeam = count($teamUserIDs);


            // auto allocation for PT only
            if(isset($_POST['autoallocate']) && $_POST['autoallocate'] == 'on') {

                // indicate that there are staff in the team with the specific specialisation
                if (count($PTUsers) > 0 || count($FTUsers) > 0) {
    
                    $validSpecialisation = TRUE;
                
    
                    // check if endDate is not less than startDate
                    if ($endDate >= $startDate) {
        
                        $validDate = TRUE;
        
                    } else {
                        echo "<script type='text/javascript'>";
                        echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
                        echo "window.location = 'Manager_addTask.php';";
                        echo "</script>";
                    }
    
                } else {
                    echo "<script type='text/javascript'>";
                    echo "alert('There are no staff with ".$specialisationName.". Please select other specialisation.');";
                    echo "window.location = 'Manager_addTask.php';";
                    echo "</script>";
                }

                $autoallocate = TRUE;

                if ($validSpecialisation && $validDate) {
                    header('location: Manager_addUsersTask.php?taskname='.$taskName.'&taskdesc='.$taskDesc.'&specialisationidname='.$specialisationIDName.'&startdate='.$startDate.'&enddate='.$endDate.'&priority='.$priority.'&autoallocate='.$autoallocate.'&numstaffteam='.$numStaffTeam.'&mainteamid='.$teamID);
                }


            // manual allocation for both FT and PT
            } else {

                // indicate that there are staff in the team with the specific specialisation
                if ($numStaffTeam > 0) {
    
                    $validSpecialisation = TRUE;
                
    
                    // check if endDate is not less than startDate
                    if ($endDate >= $startDate) {
        
                        $validDate = TRUE;
        
                    } else {
                        echo "<script type='text/javascript'>";
                        echo "alert('Invalid date. Please make sure the Start Date is not more than the End Date.');";
                        echo "window.location = 'Manager_addTask.php';";
                        echo "</script>";
                    }
    
                } else {
                    echo "<script type='text/javascript'>";
                    echo "alert('There are no staff with ".$specialisationName.". Please select other specialisation.');";
                    echo "window.location = 'Manager_addTask.php';";
                    echo "</script>";
                }

                $isManual = TRUE;
                
                if ($validSpecialisation && $validDate) {
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
        <?php include_once('navigation.php');?>
        
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
                                            Auto Allocate <input type="checkbox" name="autoallocate" value="on">
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
