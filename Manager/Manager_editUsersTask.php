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

        $userStatus = 1;
        $userID = 2;


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

        if(isset($_GET['numstaffteam'])) {
            $numStaffTeam = $_GET['numstaffteam'];
        }

        if(isset($_REQUEST['specialisationidname'])) {
            $specialisationIDName = $_REQUEST['specialisationidname'];

            $specialisationIDNameE = explode(" ", $specialisationIDName);
    
            $specialisationID = $specialisationIDNameE[0];
            
            $specialisationName = $specialisationIDNameE[1];
        }

        if(isset($_GET['mainteamidname'])) {
            $teamIDName = $_GET['mainteamidname'];

            $teamIDNameE = explode(",", $teamIDName);

            $mainTeamID = $teamIDNameE[0];
            
            $mainTeamName = $teamIDNameE[1];
        }

        if(isset($_GET['autoallocate']) && $_GET['autoallocate'] == 'on') {
            $autoallocate = $_GET['autoallocate'];
        }

        if(isset($_GET['ismanual'])) {
            $isManual = $_GET['ismanual'];
        }

        if(isset($_GET['maintaskid'])) {
            $mainTaskID = $_GET['maintaskid'];
        }

        if(isset($_GET['statusid'])) {
            $statusID = $_GET['statusid'];
        }



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

        if(isset($_POST['mainteamidname'])) {
            $teamIDName = $_POST['mainteamidname'];

            $teamIDNameE = explode(",", $teamIDName);

            $mainTeamID = $teamIDNameE[0];
            
            $mainTeamName = $teamIDNameE[1];
        }

        if(isset($_POST['specialisationidname'])) {
            $specialisationIDName = $_POST['specialisationidname'];

            $specialisationIDNameE = explode(" ", $specialisationIDName);
    
            $specialisationID = $specialisationIDNameE[0];
            
            $specialisationName = $specialisationIDNameE[1];
        }

        if(isset($_POST['numstaff'])) {
            $numStaff = $_POST['numstaff'];
        }

        if(isset($_POST['numstaffteam'])) {
            $numStaffTeam = $_POST['numstaffteam'];
        }

        if(isset($_POST['maintaskid'])) {
            $mainTaskID = $_POST['maintaskid'];
        }

        if(isset($_POST['statusid'])) {
            $statusID = $_POST['statusid'];
        }


        if (isset($_GET['ismanual']) == 1) {

            // get FT users of the specific task
            $sql = "SELECT b.MainTaskID, a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, a.SpecialisationID FROM existinguser a
                    INNER JOIN task b ON a.UserID = b.UserID
                    WHERE b.MainTaskID = ".$mainTaskID."
                    AND a.Role = 'FT';";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $FTTaskUsers = $result->fetch_all(MYSQLI_ASSOC);


            // get PT users of the specific task
            $sql = "SELECT b.MainTaskID, a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, a.SpecialisationID FROM existinguser a
                    INNER JOIN task b ON a.UserID = b.UserID
                    WHERE b.MainTaskID = ".$mainTaskID."
                    AND a.Role = 'PT';";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $PTTaskUsers = $result->fetch_all(MYSQLI_ASSOC);

            //echo " <br> sql1 = ".$sql;


            // get FT Users that does not have tasks
            $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks,"
                . " (SELECT COUNT(*) FROM leaves WHERE UserID = a.UserID AND (StartDate BETWEEN '".$startDate."' AND '".$endDate."' OR EndDate BETWEEN '".$startDate."' AND '".$endDate."') AND Status = 1) AS onLeave"
                . " FROM existinguser a"
                . " INNER JOIN team b ON a.UserID = b.UserID"
                . " LEFT JOIN task d ON b.UserID = d.UserID"
                . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
                . " WHERE a.SpecialisationID = ".$specialisationID
                . " AND a.Status = ".$userStatus
                . " AND b.MainTeamID = ".$mainTeamID
                . " AND a.Role = 'FT'";

            if (count($FTTaskUsers) > 0) {
                $sql .= " AND a.UserID NOT IN (".$FTTaskUsers[0]['UserID'];

                if (count($FTTaskUsers) > 1) {
                    for ($i = 1; $i < count($FTTaskUsers); $i++) {
                        $sql .= ", ".$FTTaskUsers[$i]['UserID'];
                    }
                }
                $sql .= ")";
            }
            $sql .= " GROUP BY a.UserID, onLeave"
                . " HAVING onLeave = 0"
                . " ORDER BY totalTasks ASC;";

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $FTUsers = $result->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();


            // get PT Users that does not have tasks
            $sql = "SELECT a.UserID, concat(a.FirstName,' ',a.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks FROM existinguser a"
                . " INNER JOIN team b ON a.UserID = b.UserID"
                . " LEFT JOIN schedule c ON a.UserID = c.UserID"
                . " LEFT JOIN task d ON c.UserID = d.UserID"
                . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
                . " WHERE a.SpecialisationID = ".$specialisationID
                . " AND a.Status = ".$userStatus
                . " AND b.MainTeamID = ".$mainTeamID
                . " AND a.Role = 'PT'"
                . " AND c.WorkDate >= '".$startDate."' AND c.WorkDate <= '".$endDate."'";

            if (count($PTTaskUsers) > 0) {

                $sql .= " AND a.UserID NOT IN (".$PTTaskUsers[0]['UserID'];

                if (count($PTTaskUsers) > 1) {
                    for ($i = 1; $i < count($PTTaskUsers); $i++) {
                        $sql .= ", ".$PTTaskUsers[$i]['UserID'];
                    }
                }
                $sql .= ")";
            }
            $sql .= " GROUP BY a.UserID"
                . " ORDER BY totalTasks ASC;";

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $PTUsers = $result->fetch_all(MYSQLI_ASSOC);
        }



        if(isset($_POST['editTask'])) {

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
                }
                

                $stmt = $conn->prepare($sql);
                
                $stmt->execute();
                $result = $stmt->get_result();
                $FTUsers = $result->fetch_all(MYSQLI_ASSOC);


                if ($numStaffTeam >= $numStaff) {
                
                    // Update into TaskInfo query
                    $stmt = $conn->prepare("UPDATE taskinfo SET SpecialisationID=?,TaskName=?,TaskDesc=?,StartDate=?,DueDate=?,NumStaff=?,Priority=?,Status=? WHERE MainTaskID=?");

                    $stmt->bind_param("issssiiii",$specialisationID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$statusID,$mainTaskID);

                    if ($stmt->execute()) {

                        if (count($PTUsers) > 0 || count($FTUsers) > 0) {

                            // Delete users assigned to the specific task
                            $stmt = $conn->prepare("DELETE FROM task WHERE MainTaskID = ?");

                            $stmt->bind_param("i",$mainTaskID);
                                
                            if ($stmt->execute()) {
                                    
                                // Insert into Task query
                                $stmt = $conn->prepare("INSERT INTO task (MainTeamID,MainTaskID,UserID) VALUES (?,?,?)");

                                if (count($PTUsers) > 0) {

                                    foreach ($PTUsers as $user):

                                        $stmt->bind_param("iii",$mainTeamID,$mainTaskID, $user['UserID']);

                                        $stmt->execute();

                                    endforeach;

                                    if (count($PTUsers) < $numStaff) {

                                        foreach ($FTUsers as $user):

                                            $stmt->bind_param("iii",$mainTeamID,$mainTaskID, $user['UserID']);

                                            $stmt->execute();

                                        endforeach;

                                        echo "<script type='text/javascript'>";
                                        echo "alert('Task has been auto allocated.');";
                                        echo "window.location = 'Manager_viewTask.php?maintaskid=".$mainTaskID."';";
                                        echo "</script>";

                                    }
                                } else {
                                    foreach ($FTUsers as $user):

                                        $stmt->bind_param("iii",$mainTeamID,$mainTaskID, $user['UserID']);

                                        $stmt->execute();

                                    endforeach;

                                    echo "<script type='text/javascript'>";
                                    echo "alert('Task has been auto allocated.');";
                                    echo "window.location = 'Manager_viewTask.php?maintaskid=".$mainTaskID."';";
                                    echo "</script>";
                                }
                                    
                            }
                        }
                    } else {
                        echo "FAILED! Error: " . $stmt->error;
                    }
                            
                } else {
                    $autoallocate = TRUE;
                    echo "<script type='text/javascript'>";
                    echo "alert('There are ".$numStaffTeam." with ".$specialisationName." in ".$mainTeamName.". The indicated number of staff with the specialisation needed for the task is more than what is available in the team');";
                    echo "window.location = 'Manager_editUsersTask.php?taskname=".$taskName."&taskdesc=".$taskDesc."&specialisationidname=".$specialisationIDName."&startdate=".$startDate."&enddate=".$endDate."&priority=".$priority."&autoallocate=".$autoallocate."&numstaffteam=".$numStaffTeam."&mainteamidname=".$teamIDName."&maintaskid=".$mainTaskID."&statusid=".$statusID."';";
                    echo "</script>";
                }
            
            // manual allocation
            } else if (isset($_POST['selectStaff'])) {

                $selectStaff = $_POST['selectStaff'];

                $numStaff = count($selectStaff);

                // Update into TaskInfo query
                $stmt = $conn->prepare("UPDATE taskinfo SET SpecialisationID=?,TaskName=?,TaskDesc=?,StartDate=?,DueDate=?,NumStaff=?,Priority=?,Status=? WHERE MainTaskID=?");

                $stmt->bind_param("issssiiii",$specialisationID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$statusID,$mainTaskID);

                if ($stmt->execute()) {

                    // Delete users assigned to the specific task
                    $stmt = $conn->prepare("DELETE FROM task WHERE MainTaskID = ?");

                    $stmt->bind_param("i",$mainTaskID);

                    if ($stmt->execute()) {

                        // Insert into TaskInfo query
                        $stmt = $conn->prepare("INSERT INTO task (MainTeamID,MainTaskID,UserID) VALUES (?,?,?)");

                        foreach ($selectStaff as $userIDs) {

                            $stmt->bind_param("iii",$mainTeamID,$mainTaskID, $userIDs);

                            $stmt->execute();

                            echo "<script type='text/javascript'>";
                            echo "alert('Task has updated.');";
                            echo "window.location = 'Manager_viewTask.php?maintaskid=".$mainTaskID."';";
                            echo "</script>";
                        }
                    }
                } else {
                    echo "FAILED! Error: " . $stmt->error;
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
            <h2 class="contentHeader">Select Staff for Task</h2>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editTask" action="Manager_editUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                        <input type="hidden" name="taskname" value="<?php echo $taskName; ?>">
                                        <input type="hidden" name="taskdesc" value="<?php echo $taskDesc; ?>">
                                        <input type="hidden" name="specialisationidname" value="<?php echo $specialisationIDName; ?>">
                                        <input type="hidden" name="startdate" value="<?php echo $startDate; ?>">
                                        <input type="hidden" name="enddate" value="<?php echo $endDate; ?>">
                                        <input type="hidden" name="priority" value="<?php echo $priority; ?>">
                                        <input type="hidden" name="numstaffteam" value="<?php echo $numStaffTeam; ?>">
                                        <input type="hidden" name="mainteamidname" value="<?php echo $teamIDName; ?>">
                                        <input type="hidden" name="maintaskid" value="<?php echo $mainTaskID; ?>">
                                        <input type="hidden" name="statusid" value="<?php echo $statusID; ?>">

                                        <?php
                                        if (isset($_GET['autoallocate']) && $_GET['autoallocate'] == 1) { ?>

                                            <input type="number" id="numstaff" name="numstaff">

                                        <?php } else if (isset($_GET['ismanual']) && $_GET['ismanual'] == 1) { ?>

                                            <label for="userid">

                                                <p class="details">Staff that is allocated to the task: </p>

                                                <?php
                                                if (count($FTTaskUsers) > 0 || count($PTTaskUsers) > 0) {

                                                    echo "<p class='details'>Full-Time</p>";
                                                
                                                    if (count($FTTaskUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        for ($i = 0; $i < count($FTTaskUsers); $i++){
                                                            if ($FTTaskUsers[$i]['SpecialisationID'] == $specialisationID) {
                                                                echo "<div class='checkboxes'><input type='checkbox' name='selectStaff[]' value='". $FTTaskUsers[$i]['UserID']."' checked>" . $FTTaskUsers[$i]['fullName']."</div>";
                                                            } else {
                                                                echo "(Not in the selected specialisation) ".$FTTaskUsers[$i]['fullName'];
                                                            }
                                                        }
                                                    ?>

                                                        </div>

                                                    <?php
                                                    } else {
                                                        echo "None.";
                                                    }

                                                    echo "<p class='details'>Part-Time</p>";
                                                
                                                    if (count($PTTaskUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        for ($i = 0; $i < count($PTTaskUsers); $i++){
                                                            if ($PTTaskUsers[$i]['SpecialisationID'] == $specialisationID) {
                                                                echo "<div class='checkboxes'><input type='checkbox' name='selectStaff[]' value='". $PTTaskUsers[$i]['UserID']."' checked>" . $PTTaskUsers[$i]['fullName']."</div>";
                                                            } else {
                                                                echo "(Not in the selected specialisation) ".$PTTaskUsers[$i]['fullName'];
                                                            }
                                                        }
                                                    ?>
                                                    
                                                        </div>

                                                    <?php
                                                    } else {
                                                        echo "None.";
                                                    }
                                                } else {
                                                    echo "None.";
                                                }

                                                echo "<p class='details'>Staff that is not allocated to the task: </p>";

                                                if (count($FTUsers) > 0 || count($PTUsers) > 0) {

                                                    echo "<p class='details'>Full-Time</p>";
                                                
                                                    if (count($FTUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        foreach ($FTUsers as $user):
                                                            echo "<div class='checkboxes'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']."</div>";
                                                        endforeach;
                                                    ?>

                                                        </div>

                                                    <?php
                                                    } else {
                                                        echo "None.";
                                                    }

                                                    echo "<p class='details'>Part-Time</p>";
                                                
                                                    if (count($PTUsers) > 0) {
                                                    ?>

                                                        <div class="checkbox-container">

                                                    <?php
                                                        foreach ($PTUsers as $user):
                                                            echo "<div class='checkboxes'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']."</div>";
                                                        endforeach;
                                                    ?>

                                                        </div>

                                                    <?php
                                                    } else {
                                                        echo "None.";
                                                    }
                                                } else {
                                                    echo "None.";
                                                }
                                        
                                            } ?></label>
                                    </div>
                                
                                </div>

                                <button name="editTask" type="submit" class="btn">Save</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>

</body>
</html>
