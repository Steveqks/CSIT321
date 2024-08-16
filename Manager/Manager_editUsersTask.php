<?php
    session_start();
    
    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];

    // Connect to the database
    $conn = OpenCon();

    $userStatus = 1;

    $showManualForm = FALSE;


    // From Manager_editTask.php
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

    if(isset($_GET['allocatetype'])) {

        $allocateType = $_GET['allocatetype'];

    } else if(isset($_POST['allocatetype'])) {

        $allocateType = $_POST['allocatetype'];

    } else {

        $allocateType = "manual";

    }
    
    if(isset($_GET['taskname'])) {
        $taskName = $_GET['taskname'];
    }

    if(isset($_GET['taskdesc'])) {
        $taskDesc = $_GET['taskdesc'];
    }

    if(isset($_GET['startdate'])) {
        $startDate = $_GET['startdate'];
    }

    if(isset($_GET['enddate'])) {
        $endDate = $_GET['enddate'];
    }

    if(isset($_GET['priority'])) {
        $priority = $_GET['priority'];
    }

    if(isset($_GET['mainprojectid'])) {
        $mainProjectID = $_GET['mainprojectid'];
    }

    if(isset($_POST['maingroupid'])) {
        $mainGroupID = $_POST['maingroupid'];
    }

    if(isset($_POST['maintaskid'])) {
        $mainTaskID = $_POST['maintaskid'];
    }

    if(isset($_POST['statusid'])) {
        $taskStatus = $_POST['statusid'];
    }


    // From Manager_editUsersTask.php
    if(isset($_POST['numstaff'])) {
        $numStaff = $_POST['numstaff'];
    }
    
    if(isset($_GET['maintaskid'])) {
        $mainTaskID = $_GET['maintaskid'];
    }

    if(isset($_GET['statusid'])) {
        $taskStatus = $_GET['statusid'];
    }



    // Checking - Valid Date
    if(isset($_POST['editTask'])) {

        // Check if endDate is not less than startDate
        if ($endDate >= $startDate) {

            // Check if StartDate and EndDate = Project's StartDate and EndDate
            $sql = "SELECT StartDate, EndDate FROM projectinfo
                    WHERE MainProjectID = ".$mainProjectID;
            
            $stmt = $conn->prepare($sql);
                    
            $stmt->execute();
            $result = $stmt->get_result();
            $projectDate = $result->fetch_assoc();

            if ($startDate <= $projectDate['StartDate'] && $endDate >= $projectDate['EndDate']) {
                
                // Close the database connection
                $stmt->close();
                CloseCon($conn);

                header("Location: Manager_editTask.php?error=Invalid date. Start Date or End Date is not within the Project's timeline.");
                exit();

            }
        } else {
            header("Location: Manager_editTask.php?error=Invalid date. Please make sure the Start Date is not more than the End Date.");
            exit();
        }
    }


    // FORM
    // get Task Group details
    $sql = "SELECT b.MainGroupID, b.GroupName, c.SpecialisationName, e.MainProjectID
            FROM taskinfo d
            INNER JOIN task a ON a.MainTaskID = d.MainTaskID
            INNER JOIN specialisationgroupinfo b ON b.MainGroupID = a.MainGroupID
            INNER JOIN specialisation c ON b.SpecialisationID = c.SpecialisationID
            INNER JOIN projectinfo e ON e.MainProjectID = d.MainProjectID
            WHERE a.MainTaskID = ".$mainTaskID."
            GROUP BY b.MainGroupID, b.GroupName, c.SpecialisationName, e.MainProjectID;";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $taskGroupDetails = $result->fetch_all(MYSQLI_ASSOC);


    // get group project details
    $sql = "SELECT a.MainGroupID, a.GroupName, c.SpecialisationName FROM specialisationgroupinfo a
            INNER JOIN project d ON d.MainGroupID = a.MainGroupID
            INNER JOIN specialisation c ON a.SpecialisationID = c.SpecialisationID
            WHERE d.MainProjectID = ".$mainProjectID."
            AND a.MainGroupID NOT IN (".$taskGroupDetails[0]['MainGroupID'];

    if (count($taskGroupDetails) > 1) {
        for($i = 1; $i < count($taskGroupDetails); $i++) {
            $sql .= ", ".$taskGroupDetails[$i]['MainGroupID'];
        }
    }

    $sql .= ") GROUP BY a.MainGroupID, a.GroupName, c.SpecialisationName;";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $groupProjectDetails = $result->fetch_all(MYSQLI_ASSOC);



    if (isset($_POST['editUsersTask'])) {

        // Get the number of staff in the Specialisation Group
        $sql = "SELECT COUNT(a.UserID) AS numStaffGroup FROM specialisationgroup a
                INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                WHERE b.MainProjectID = ".$mainProjectID."
                AND a.MainGroupID = ".$mainGroupID;

        $stmt = $conn->prepare($sql);
                
        $stmt->execute();
        $result = $stmt->get_result();
        $numStaffTeam = $result->fetch_assoc();

        $totalNoStaff = $numStaffTeam['numStaffGroup'];



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
                    AND b.MainGroupID = ".$mainGroupID."
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

        //echo $sql;

        // Get FT staff that is in the same Specialisation Group as the selected Project
        $sql = "SELECT a.UserID, (SELECT COUNT(*) FROM leaves WHERE UserID = a.UserID AND (StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR EndDate BETWEEN '".$startDate."' AND '".$endDate."') AND Status = 1) AS onLeave FROM existinguser a
                INNER JOIN specialisationgroup b ON a.UserID = b.UserID
                INNER JOIN project c ON b.MainGroupID = c.MainGroupID
                WHERE a.Role = 'FT' AND c.MainProjectID = ".$mainProjectID."
                AND b.MainGroupID = ".$mainGroupID."
                GROUP BY a.UserID
                HAVING onLeave = 0";

        $stmt = $conn->prepare($sql);
                
        $stmt->execute();
        $result = $stmt->get_result();
        $FTUsers = $result->fetch_all(MYSQLI_ASSOC);

        //echo "<br><br>". $sql;


        $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, d.MainGroupID,

                (SELECT IFNULL(SUM(b.Status),0) AS totalTasks FROM taskinfo b
                INNER JOIN task c ON b.MainTaskID = c.MainTaskID
                WHERE b.Status = ".$taskStatus."
                AND (b.StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR b.DueDate BETWEEN '".$startDate."' AND '".$endDate."')
                AND b.MainTaskID != ".$mainTaskID."
                AND c.UserID = a.UserID) AS totalTasks,

                (SELECT IFNULL(SUM(b.Status),0) AS totalTasks FROM taskinfo b
                INNER JOIN task c ON b.MainTaskID = c.MainTaskID
                WHERE b.Status = ".$taskStatus."
                AND b.MainTaskID = ".$mainTaskID."
                AND c.UserID = a.UserID) AS currentTask

                FROM existinguser a
                INNER JOIN specialisationgroup d ON a.UserID = d.UserID
                WHERE d.MainGroupID = ".$mainGroupID."
                AND a.UserID IN (";

                
        if ($allocateType == "manual") {

            $FTTasksUsers = array();
            $PTTasksUsers = array();

            if (count($FTUsers) == 0 && count($PTUsers) == 0) {
                
                
                    // Get the number of staff in the Specialisation Group
                    $sql = "SELECT a.GroupName FROM specialisationgroupinfo a
                    INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                    WHERE b.MainProjectID = ".$mainProjectID."
                    AND a.MainGroupID = ".$mainGroupID;
    
                    $stmt = $conn->prepare($sql);
                            
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $groupName = $result->fetch_assoc();

                    // Close the database connection
                    $stmt->close();
                    CloseCon($conn);
    
                    header("Location: Manager_editUsersTask.php?allocatetype=".$allocateType."&taskname=".$taskName."&taskdesc=".$taskDesc."&enddate=".$endDate."&startdate=".$startDate."&priority=".$priority."&mainprojectid=".$mainProjectID."&statusid=".$taskStatus."&maintaskid=".$mainTaskID."&allocatetype=".$allocateType."&maingroupid=".$mainGroupID."&error=There are no staff available in ".$groupName['GroupName'].". Please contact your Company Admin.");
                    exit();
                
            } else {

                if (count($FTUsers) > 0) {
                    
                    $sql .= $FTUsers[0]['UserID'];
    
                    if (count($FTUsers) > 1) {
                        for ($i = 1; $i < count($FTUsers); $i++) {
                            $sql .= ", ".$FTUsers[$i]['UserID'];
                        }
                    }
    
                    $sql .= ") GROUP BY a.UserID, fullName
                            ORDER BY totalTasks ASC;";
    
                    $stmt = $conn->prepare($sql);
                            
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $FTTasksUsers = $result->fetch_all(MYSQLI_ASSOC);
    
                }
                
                if (count($PTUsers) > 0) {
    
                    $sql .= $PTUsers[0]['UserID'];
    
                    if (count($PTUsers) > 1) {
                        for ($i = 1; $i < count($PTUsers); $i++) {
                            $sql .= ", ".$PTUsers[$i]['UserID'];
                        }
                    }
    
                    $sql .= ") GROUP BY a.UserID, fullName
                            ORDER BY totalTasks ASC;";
    
                    $stmt = $conn->prepare($sql);
    
                    //echo "<br><br>".$sql;
                            
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $PTTasksUsers = $result->fetch_all(MYSQLI_ASSOC);
    
                }
    
                if (count($FTTasksUsers) > 0 || count($PTTasksUsers)) {
                    
                    $showManualForm = TRUE;
                    
                    if (isset($_POST['selectStaff'])) {
    
                        $selectStaff = $_POST['selectStaff'];
    
                        $numStaff = count($selectStaff);
    
                        // Update TaskInfo query
                        $stmt = $conn->prepare("UPDATE taskinfo SET MainProjectID=?,TaskName=?,TaskDesc=?,StartDate=?,DueDate=?,NumStaff=?,Priority=?,Status=? WHERE MainTaskID=?");
    
                        $stmt->bind_param("issssiiii",$mainProjectID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus,$mainTaskID);
    
                        if ($stmt->execute()) {
    
                            // Delete users assigned to the specific task
                            $stmt = $conn->prepare("DELETE FROM task WHERE MainTaskID = ?");
    
                            $stmt->bind_param("i",$mainTaskID);
    
                            if ($stmt->execute()) {
    
                                // Insert into TaskInfo query
                                $stmt = $conn->prepare("INSERT INTO task (MainGroupID,MainTaskID,UserID) VALUES (?,?,?)");
    
                                foreach ($selectStaff as $userIDs) {
    
                                    $stmt->bind_param("iii",$mainGroupID, $mainTaskID, $userIDs);
    
                                    $stmt->execute();
                                }

                                $newTaskID = $stmt->insert_id;

                                if ($newTaskID > 0) {

                                    // Close the database connection
                                    $stmt->close();
                                    CloseCon($conn);
    
                                    header("Location: Manager_viewTask.php?&maintaskid=".$mainTaskID);
                                    exit();

                                }
                            }
                        }
                    }
                }

            }


        } else if ($allocateType == "auto") {

            if (count($FTUsers) > 0) {
                    
                $sql .= $FTUsers[0]['UserID'];

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
            } else if (count($PTUsers) > 0) {

                $sql .= $PTUsers[0]['UserID'];

                if (count($PTUsers) > 1) {
                    for ($i = 1; $i < count($PTUsers); $i++) {
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

            } else {
                
                // Get the number of staff in the Specialisation Group
                $sql = "SELECT a.GroupName FROM specialisationgroupinfo a
                INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                WHERE b.MainProjectID = ".$mainProjectID."
                AND a.MainGroupID = ".$mainGroupID;

                $stmt = $conn->prepare($sql);
                        
                $stmt->execute();
                $result = $stmt->get_result();
                $groupName = $result->fetch_assoc();

                // Close the database connection
                $stmt->close();
                CloseCon($conn);

                header("Location: Manager_editUsersTask.php?allocatetype=".$allocateType."&taskname=".$taskName."&taskdesc=".$taskDesc."&enddate=".$endDate."&startdate=".$startDate."&priority=".$priority."&mainprojectid=".$mainProjectID."&statusid=".$taskStatus."&maintaskid=".$mainTaskID."&allocatetype=".$allocateType."&maingroupid=".$mainGroupID."&error=There are no staff available in ".$groupName['GroupName'].". Please contact your Company Admin.");
                exit();
            }
            

            //echo "<br><br> auto". $sql;

            $stmt = $conn->prepare($sql);
                    
            $stmt->execute();
            $result = $stmt->get_result();
            $allUsers = $result->fetch_all(MYSQLI_ASSOC);


            if (count($allUsers) == 0) {
                
                // Get the number of staff in the Specialisation Group
                $sql = "SELECT a.GroupName FROM specialisationgroupinfo a
                INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                WHERE b.MainProjectID = ".$mainProjectID."
                AND a.MainGroupID = ".$mainGroupID;

                $stmt = $conn->prepare($sql);
                        
                $stmt->execute();
                $result = $stmt->get_result();
                $groupName = $result->fetch_assoc();

                // Close the database connection
                $stmt->close();
                CloseCon($conn);

                header("Location: Manager_editUsersTask.php?allocatetype=".$allocateType."&taskname=".$taskName."&taskdesc=".$taskDesc."&enddate=".$endDate."&startdate=".$startDate."&priority=".$priority."&mainprojectid=".$mainProjectID."&statusid=".$taskStatus."&maintaskid=".$mainTaskID."&maingroupid=".$mainGroupID."&error=There are no staff available in ".$groupName['GroupName'].". Please contact your Company Admin.");
                exit();

            } else {

                if ($totalNoStaff >= $numStaff) {

                    if (count($allUsers) > 0) {

                        // Insert into TaskInfo query
                        $stmt = $conn->prepare("UPDATE taskinfo SET MainProjectID=?,TaskName=?,TaskDesc=?,StartDate=?,DueDate=?,NumStaff=?,Priority=?,Status=? WHERE MainTaskID=?");

                        $stmt->bind_param("issssiiii",$mainProjectID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus,$mainTaskID);

                        if ($stmt->execute()) {

                            // Delete users assigned to the specific task
                            $stmt = $conn->prepare("DELETE FROM task WHERE MainTaskID = ?");
        
                            $stmt->bind_param("i",$mainTaskID);
        
                            if ($stmt->execute()) {
        
                                // Insert into TaskInfo query
                                $stmt = $conn->prepare("INSERT INTO task (MainGroupID,MainTaskID,UserID) VALUES (?,?,?)");

                                foreach ($allUsers as $allUsersDetails) {

                                    $stmt->bind_param("iii",$allUsersDetails['MainGroupID'], $mainTaskID, $allUsersDetails['UserID']);

                                    $stmt->execute();
                                }

                                $newTaskID = $stmt->insert_id;

                                if ($newTaskID > 0) {

                                    // Close the database connection
                                    $stmt->close();
                                    CloseCon($conn);
    
                                    header("Location: Manager_viewTask.php?&maintaskid=".$mainTaskID);
                                    exit();

                                }
                            }
                        }

                    }

                } else {

                    // Get the number of staff in the Specialisation Group
                    $sql = "SELECT a.GroupName FROM specialisationgroupinfo a
                    INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                    WHERE b.MainProjectID = ".$mainProjectID."
                    AND a.MainGroupID = ".$mainGroupID;

                    $stmt = $conn->prepare($sql);
                            
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $groupName = $result->fetch_assoc();
                    
                    // Close the database connection
                    $stmt->close();
                    CloseCon($conn);

                    header("Location: Manager_editUsersTask.php?allocatetype=".$allocateType."&taskname=".$taskName."&taskdesc=".$taskDesc."&enddate=".$endDate."&startdate=".$startDate."&priority=".$priority."&mainprojectid=".$mainProjectID."&error=There are ".$totalNoStaff." staff in ".$groupName['GroupName'].". The indicated number of staff with the specialisation needed for the task is more than what is available in the specialisation group.");
                    exit();
                }

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
            <?php if($allocateType === "auto") { ?>

                <h2 class="contentHeader">Select Number of Staff</h2>

            <?php } else if ($allocateType === "manual"){ ?>

                <h2 class="contentHeader">Select Staff for Task</h2>
            <?php }?>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editUsersTask" action="Manager_editUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                        <input type="hidden" name="taskname" value="<?php echo $taskName; ?>">
                                        <input type="hidden" name="taskdesc" value="<?php echo $taskDesc; ?>">
                                        <input type="hidden" name="startdate" value="<?php echo $startDate; ?>">
                                        <input type="hidden" name="enddate" value="<?php echo $endDate; ?>">
                                        <input type="hidden" name="priority" value="<?php echo $priority; ?>">
                                        <input type="hidden" name="mainprojectid" value="<?php echo $mainProjectID; ?>">
                                        <input type="hidden" name="allocatetype" value="<?php echo $allocateType; ?>">
                                        <input type="hidden" name="maintaskid" value="<?php echo $mainTaskID; ?>">
                                        <input type="hidden" name="statusid" value="<?php echo $taskStatus; ?>">

                                        <?php
                                        if ($allocateType === "auto") { ?>

                                            <label for="maingroupid">Group</label>
                                            <select name='maingroupid' required>
                                                <?php
                                                foreach ($taskGroupDetails as $groupName):
                                                    echo "<option value='". $groupName['MainGroupID']."'>". $groupName['GroupName']." ( ".$groupName['SpecialisationName']." )</option>";
                                                endforeach;

                                                foreach ($groupProjectDetails as $groupName):
                                                    echo "<option value='". $groupName['MainGroupID']."'>". $groupName['GroupName']." ( ".$groupName['SpecialisationName']." )</option>";
                                                endforeach;
                                                ?>
                                            </select>

                                            <label for="numstaff">Required Number of Staff</label>
                                            <input type="number" id="numstaff" name="numstaff" required>



                                        <?php } else if ($allocateType === "manual") {
                                                    if ($showManualForm == FALSE) { ?>

                                            <label for="maingroupid">Group</label>
                                            <select name='maingroupid' required>
                                                <?php
                                                foreach ($taskGroupDetails as $groupName):
                                                    echo "<option value='". $groupName['MainGroupID']."'>". $groupName['GroupName']." ( ".$groupName['SpecialisationName']." )</option>";
                                                endforeach;

                                                foreach ($groupProjectDetails as $groupName):
                                                    echo "<option value='". $groupName['MainGroupID']."'>". $groupName['GroupName']." ( ".$groupName['SpecialisationName']." )</option>";
                                                endforeach;
                                                ?>
                                            </select>

                                            <?php } else { ?>

                                            <input type="hidden" name="maingroupid" value="<?php echo $mainGroupID; ?>">

                                            <label for="userid">

                                                <p class="details">Staff that is allocated to the task: </p>

                                                <?php
                                                if (count($FTTasksUsers) > 0 || count($PTTasksUsers) > 0) {

                                                    echo "<p class='details'>Full-Time</p>";
                                                
                                                    if (count($FTTasksUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        foreach ($FTTasksUsers as $user) {
                                                            if ($user['currentTask'] > 0) {
                                                                echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."' checked>" . $user['fullName']." ( ".$user['totalTasks']." Task(s) )</div>";
                                                            } else {
                                                                echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']." ( ".$user['totalTasks']." Task(s) )</div>";
                                                            }
                                                        }
                                                    ?>

                                                        </div>

                                                    <?php
                                                    } else {
                                                        echo "No Full-Time Staff available.";
                                                    }

                                                    echo "<p class='details'>Part-Time</p>";
                                                
                                                    if (count($PTTasksUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        foreach ($PTTasksUsers as $user) {
                                                            if ($user['currentTask'] > 0) {
                                                                echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."' checked>" . $user['fullName']." ( ".$user['totalTasks']." Task(s) )</div>";
                                                            } else {
                                                                echo "<div class='checkbox-team'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']." ( ".$user['totalTasks']." Task(s) )</div>";
                                                            }
                                                        }
                                                    } else {
                                                        echo "No Part-Time Staff available.";
                                                    }
                                                    ?>
                                                    
                                                        </div>

                                                    
                                                <?php } ?>
                                                </label>
                                                <?php
                                            }
                                        } ?>
                                            
                                    </div>
                                
                                </div>
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="editUsersTask" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>

</body>
</html>