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

    $taskStatus = 1;
    $userStatus = 1;

    $showAutoForm = FALSE;
    $showManualForm = FALSE;

    // From Manager_addTask.php
    if(isset($_POST['taskname'])) {
        $taskName = $_POST['taskname'];
    }

    if(isset($_POST['taskdesc'])) {
        $taskDesc = $_POST['taskdesc'];
    }

    if(isset($_POST['startdate'])) {
        $startDate = $_POST['startdate'];
    }

    if(isset($_POST['enddate'])) {
        $endDate = $_POST['enddate'];
    }

    if(isset($_POST['priority'])) {
        $priority = $_POST['priority'];
    }

    if(isset($_POST['mainprojectid'])) {
        $mainProjectID = $_POST['mainprojectid'];
    }

    if(isset($_POST['allocatetype'])) {

        $allocateType = $_POST['allocatetype'];

    } else {

        $allocateType = "manual";

    }


    // From Manager_addUsersTask.php
    if(isset($_POST['numstaff'])) {
        $numStaff = $_POST['numstaff'];
    }


    if(isset($_POST['addTask'])) {

        // Check if endDate is not less than startDate
        if ($endDate >= $startDate) {

            // Check if StartDate and EndDate = Project's StartDate and EndDate
            $sql = "SELECT StartDate, EndDate FROM projectinfo
                    WHERE MainProjectID = ".$mainProjectID;
            
            $stmt = $conn->prepare($sql);
                    
            $stmt->execute();
            $result = $stmt->get_result();
            $projectDate = $result->fetch_assoc();

            if ($startDate >= $projectDate['StartDate'] && $endDate <= $projectDate['EndDate']) {
                $showAutoForm = TRUE;
            } else {
                header("Location: Manager_addTask.php?error=Invalid date. Start Date or End Date is not within the Project's timeline.");
                exit();
            }
        } else {
            header("Location: Manager_addTask.php?error=Invalid date. Please make sure the Start Date is not more than the End Date.");
            exit();
        }
    }



    if (isset($_POST['addUsersTask'])) {
    
        // Get PT staff that is working on the selected dates
        // and is in the same Specialisation Group as the selected Project
        $sql = "WITH ConsecutiveWork AS (
                    SELECT a.UserID, a.WorkDate, ROW_NUMBER() OVER (PARTITION BY a.UserID ORDER BY a.WorkDate) AS rn
                    FROM schedule a
                    INNER JOIN specialisationgroup b ON a.UserID = b.UserID
                    INNER JOIN project c ON b.MainGroupID = c.MainGroupID
                    WHERE a.WorkDate BETWEEN '".$startDate."' AND '".$endDate."'
                    AND DAYOFWEEK(a.WorkDate) NOT IN (1, 7)
                    AND c.MainProjectID = ".$mainProjectID."
                ),
                GroupedConsecutiveWork AS (
                    SELECT UserID, COUNT(*) AS consecutive_days
                    FROM ConsecutiveWork
                    GROUP BY UserID
                )
                SELECT UserID FROM GroupedConsecutiveWork;";

        $stmt = $conn->prepare($sql);
                
        $stmt->execute();
        $result = $stmt->get_result();
        $PTUsers = $result->fetch_all(MYSQLI_ASSOC);

        echo $sql;

        // Get FT staff that is in the same Specialisation Group as the selected Project
        $sql = "SELECT a.UserID, (SELECT COUNT(*) FROM leaves WHERE UserID = a.UserID AND (StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR EndDate BETWEEN '".$startDate."' AND '".$endDate."') AND Status = 1) AS onLeave FROM existinguser a
                INNER JOIN specialisationgroup b ON a.UserID = b.UserID
                INNER JOIN project c ON b.MainGroupID = c.MainGroupID
                WHERE a.Role = 'FT' AND c.MainProjectID = ".$mainProjectID."
                GROUP BY a.UserID
                HAVING onLeave = 0";

        $stmt = $conn->prepare($sql);
                
        $stmt->execute();
        $result = $stmt->get_result();
        $FTUsers = $result->fetch_all(MYSQLI_ASSOC);

        echo "<br><br>". $sql;


        if ($allocateType == "auto") {

            // Get the number of staff in the Specialisation Pool
            $sql = "SELECT COUNT(a.UserID) AS numStaffGroup FROM specialisationgroup a
                    INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                    WHERE b.MainProjectID = ".$mainProjectID;

            $stmt = $conn->prepare($sql);
                    
            $stmt->execute();
            $result = $stmt->get_result();
            $numStaffTeam = $result->fetch_assoc();

            $totalNoStaff = $numStaffTeam['numStaffGroup'];


            if ($totalNoStaff >= $numStaff) {

                $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, d.MainGroupID,
                        (SELECT IFNULL(SUM(b.Status),0) AS totalTasks FROM taskinfo b
                        INNER JOIN task c ON b.MainTaskID = c.MainTaskID
                        WHERE b.Status = ".$taskStatus."
                        AND (b.StartDate >= '".$startDate."' OR b.DueDate <= '".$endDate."')) AS totalTasks
                        FROM existinguser a
                        INNER JOIN specialisationgroup d ON a.UserID = d.UserID
                        WHERE a.UserID IN (".$FTUsers[0]['UserID'];

                if (count($FTUsers) > 1) {
                    for ($i = 1; $i < count($FTUsers); $i++) {
                        $sql .= ", ".$FTUsers[$i]['UserID'];
                    }
                }
                
                if (count($PTUsers) > 0) {
                    for ($i = 0; $i < count($PTUsers); $i++) {
                        $sql .= ", ".$PTUsers[$i]['UserID'];
                    }

                    $sql .= ") GROUP BY a.UserID, fullName
                            ORDER BY totalTasks ASC
                            LIMIT ".$numStaff.";";

                } else {
                    $sql .= ") GROUP BY a.UserID, fullName
                            ORDER BY totalTasks ASC
                            LIMIT ".$numStaff.";";
                }

                echo "<br><br>". $sql;

                $stmt = $conn->prepare($sql);
                        
                $stmt->execute();
                $result = $stmt->get_result();
                $allUsers = $result->fetch_all(MYSQLI_ASSOC);


                if (count($allUsers) > 0) {

                    // Insert into TaskInfo query
                    $stmt = $conn->prepare("INSERT INTO taskinfo (MainProjectID,TaskName,TaskDesc,StartDate,DueDate,NumStaff,Priority,Status) VALUES (?,?,?,?,?,?,?,?)");

                    $stmt->bind_param("issssiii",$mainProjectID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus);

                    if ($stmt->execute()) {

                        $newMainTaskID = $stmt->insert_id;

                        // Insert into TaskInfo query
                        $stmt = $conn->prepare("INSERT INTO task (MainGroupID,MainTaskID,UserID) VALUES (?,?,?)");

                        foreach ($allUsers as $allUsersDetails) {

                            $stmt->bind_param("iii",$allUsersDetails['MainGroupID'], $newMainTaskID, $allUsersDetails['UserID']);

                            $stmt->execute();
                        }

                        header("Location: Manager_addTask.php?message=Task is successfully auto allocated.");
                    }

                }

            } else {

                // Get the number of staff in the Specialisation Pool
                $sql = "SELECT a.GroupName FROM specialisationgroupinfo a
                INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                WHERE b.MainProjectID = ".$mainProjectID;

                $stmt = $conn->prepare($sql);
                        
                $stmt->execute();
                $result = $stmt->get_result();
                $groupName = $result->fetch_assoc();

                header("Location: Manager_addUsersTask.php?error=There are ".$totalNoStaff." in ".$groupName.". The indicated number of staff with the specialisation needed for the task is more than what is available in the team.");
                exit();
            }

        } else {

        }
    }


/*
    if ((isset($_GET['ismanual']) == 1)) {

        // PT Users
        $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks FROM existinguser a"
            . " INNER JOIN team b ON a.UserID = b.UserID"
            . " LEFT JOIN schedule c ON a.UserID = c.UserID"
            . " LEFT JOIN task d ON c.UserID = d.UserID"
            . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
            . " WHERE a.SpecialisationID = ".$specialisationID
            . " AND a.Status = ".$userStatus
            . " AND b.MainTeamID = ".$mainTeamID
            . " AND a.Role = 'PT'"
            . " AND c.WorkDate >= '".$startDate."' AND c.WorkDate <= '".$endDate."'"
            . " GROUP BY a.UserID"
            . " ORDER BY totalTasks ASC";

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $PTUsers = $result->fetch_all(MYSQLI_ASSOC);


        // FT Users
        $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks,"
            . " (SELECT COUNT(*) FROM leaves WHERE UserID = a.UserID AND (StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR EndDate BETWEEN '".$startDate."' AND '".$endDate."') AND Status = 1) AS onLeave"
            . " FROM existinguser a"
            . " INNER JOIN team b ON a.UserID = b.UserID"
            . " LEFT JOIN task d ON b.UserID = d.UserID"
            . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
            . " WHERE a.SpecialisationID = ".$specialisationID
            . " AND a.Status = ".$userStatus
            . " AND b.MainTeamID = ".$mainTeamID
            . " AND a.Role = 'FT'"
            . " GROUP BY a.UserID, onLeave"
            . " HAVING onLeave = 0"
            . " ORDER BY totalTasks ASC";

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $FTUsers = $result->fetch_all(MYSQLI_ASSOC);
    }


    if(isset($_POST['addTask'])) {
        
        // auto allocation
        if(isset($_POST['numstaff'])) {

            // PT Users
            $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks FROM existinguser a"
                . " INNER JOIN team b ON a.UserID = b.UserID"
                . " LEFT JOIN schedule c ON a.UserID = c.UserID"
                . " LEFT JOIN task d ON c.UserID = d.UserID"
                . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
                . " WHERE a.SpecialisationID = ".$specialisationID
                . " AND a.Status = ".$userStatus
                . " AND b.MainTeamID = ".$mainTeamID
                . " AND a.Role = 'PT'"
                . " AND c.WorkDate >= '".$startDate."' AND c.WorkDate <= '".$endDate."'"
                . " GROUP BY a.UserID"
                . " ORDER BY totalTasks ASC"
                . " LIMIT ".$numStaff;

            $stmt = $conn->prepare($sql);
        
            $stmt->execute();
            $result = $stmt->get_result();
            $PTUsers = $result->fetch_all(MYSQLI_ASSOC);

            //echo "SQL1 ;; ".$sql;

            // FT Users
            $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks,"
                . " (SELECT COUNT(*) FROM leaves WHERE UserID = a.UserID AND (StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR EndDate BETWEEN '".$startDate."' AND '".$endDate."') AND Status = 1) AS onLeave"
                . " FROM existinguser a"
                . " INNER JOIN team b ON a.UserID = b.UserID"
                . " LEFT JOIN task d ON b.UserID = d.UserID"
                . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
                . " WHERE a.SpecialisationID = ".$specialisationID
                . " AND a.Status = ".$userStatus
                . " AND b.MainTeamID = ".$mainTeamID
                . " AND a.Role = 'FT'"
                . " GROUP BY a.UserID, onLeave"
                . " HAVING onLeave = 0"
                . " ORDER BY totalTasks ASC";
            
            if (count($PTUsers) < $numStaff) {

                $FTLimit = $numStaff - count($PTUsers);
                $sql .= " LIMIT ".$FTLimit;
            } else {
                $sql .= " LIMIT ".$numStaff;
            }

            //echo "<br> SQL2 ;; ".$sql;

            $stmt = $conn->prepare($sql);
        
            $stmt->execute();
            $result = $stmt->get_result();
            $FTUsers = $result->fetch_all(MYSQLI_ASSOC);


            if ($numStaffTeam >= $numStaff) {
            
                if (count($PTUsers) > 0 || count($FTUsers) > 0) {
                    // Insert into TaskInfo query
                    $stmt = $conn->prepare("INSERT INTO taskinfo (SpecialisationID,TaskName,TaskDesc,StartDate,DueDate,NumStaff,Priority,Status) VALUES (?,?,?,?,?,?,?,?)");

                    $stmt->bind_param("issssiii",$specialisationID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus);

                    if ($stmt->execute()) {

                        $newMainTaskID = $stmt->insert_id;

                        // Insert into TaskInfo query
                        $stmt = $conn->prepare("INSERT INTO task (MainTeamID,MainTaskID,UserID) VALUES (?,?,?)");

                        if (count($PTUsers) > 0) {

                            foreach ($PTUsers as $user):

                                $stmt->bind_param("iii",$mainTeamID,$newMainTaskID, $user['UserID']);

                                $stmt->execute();

                            endforeach;

                            if (count($PTUsers) < $numStaff) {

                                foreach ($FTUsers as $user):

                                    $stmt->bind_param("iii",$mainTeamID,$newMainTaskID, $user['UserID']);

                                    $stmt->execute();

                                endforeach;

                                header("Location: Manager_viewTaskList.php?message=Task has been auto allocated.");
                                exit();

                            }
                        } else {
                            foreach ($FTUsers as $user):

                                $stmt->bind_param("iii",$mainTeamID,$newMainTaskID, $user['UserID']);

                                $stmt->execute();

                            endforeach;

                            header("Location: Manager_viewTaskList.php?message=Task has been auto allocated.");
                            exit();
                        }

                    } else {
                        echo "FAILED! Error: " . $stmt->error;
                    }
                } else {
                    header("Location: Manager_addTask.php?error=There are no staff with ".$specialisationName.". Please select other specialisation.");
                    exit();
                }
            } else {
                $autoallocate = TRUE;

                header("Location: Manager_addUsersTask.php?error=There are ".$numStaffTeam." with ".$specialisationName." in ".$mainTeamName.". The indicated number of staff with the specialisation needed for the task is more than what is available in the team.&taskname=".$taskName."&taskdesc=".$taskDesc."&specialisationidname=".$specialisationIDName."&startdate=".$startDate."&enddate=".$endDate."&priority=".$priority."&autoallocate=".$autoallocate."&numstaffteam=".$numStaffTeam."&mainteamidname=".$teamIDName);
                exit();
            }

        // manual allocation
        } else if (isset($_POST['selectStaff'])) {

            $selectStaff = $_POST['selectStaff'];

            $numStaff = count($selectStaff);

            // Insert into TaskInfo query
            $stmt = $conn->prepare("INSERT INTO taskinfo (SpecialisationID,TaskName,TaskDesc,StartDate,DueDate,NumStaff,Priority,Status) VALUES (?,?,?,?,?,?,?,?)");

            $stmt->bind_param("issssiii",$specialisationID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus);

            if ($stmt->execute()) {

                $newMainTaskID = $stmt->insert_id;

                // Insert into TaskInfo query
                $stmt = $conn->prepare("INSERT INTO task (MainTeamID,MainTaskID,UserID) VALUES (?,?,?)");
                
                foreach ($selectStaff as $selectStaffID) {

                    $stmt->bind_param("iii",$mainTeamID,$newMainTaskID, $selectStaffID);

                    $stmt->execute();

                    header("Location: Manager_viewTaskList.php?message=Task has been allocated.");
                    exit();
                }

            } else {
                echo "FAILED! Error: " . $stmt->error;
            }
        }
    }*/
    
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
            <h2 class="contentHeader">Select Staff for Task</h2>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addUsersTask" action="Manager_addUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                        <input type="hidden" name="taskname" value="<?php echo $taskName; ?>">
                                        <input type="hidden" name="taskdesc" value="<?php echo $taskDesc; ?>">
                                        <input type="hidden" name="startdate" value="<?php echo $startDate; ?>">
                                        <input type="hidden" name="enddate" value="<?php echo $endDate; ?>">
                                        <input type="hidden" name="priority" value="<?php echo $priority; ?>">
                                        <input type="hidden" name="mainprojectid" value="<?php echo $mainProjectID; ?>">
                                        <input type="hidden" name="allocatetype" value="<?php echo $allocateType; ?>">


                                        <?php
                                        if ($allocateType == "auto") { ?>

                                            <input type="number" id="numstaff" name="numstaff">

                                        <?php } else if ($allocateType == "manual"){ ?>

                                            <label for="userid">Staff Name</label>

                                                <p style="font-weight:bold;">Full-Time</p>
                                            
                                                <div class="checkbox-container">
                                                    <?php
                                                    foreach ($FTUsers as $user):
                                                        echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']."</div>";
                                                    endforeach;
                                                    ?>
                                                </div>

                                                <p style="font-weight:bold;">Part-Time</p>

                                                <div class="checkbox-container">
                                                    <?php
                                                    foreach ($PTUsers as $user):
                                                        echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']."</div>";
                                                    endforeach;
                                                    ?>
                                                </div>
                                        
                                        <?php } ?>
                                    </div>
                                
                                </div>
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="addUsersTask" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>

</body>
</html>
