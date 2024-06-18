<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/header.css" />
    <link rel="stylesheet" href="./css/all.css" />

    <?php

        session_start();
        include 'db_connection.php';

        $conn = OpenCon();

        $taskStatus = 1;
        $userStatus = 1;
        $userID = 1;
        $employeeType = "Manager";


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

        if(isset($_GET['projectid'])) {
            $projectID = $_GET['projectid'];
        }

        if(isset($_GET['autoallocate']) && $_GET['autoallocate'] == 'on') {
            $autoallocate = $_GET['autoallocate'];
        }

        if(isset($_GET['ismanual'])) {
            $isManual = $_GET['ismanual'];
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

        if(isset($_POST['projectid'])) {
            $projectID = $_POST['projectid'];
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


        if((isset($_POST['addTask'])) || (isset($_GET['ismanual']) == 1)) {

            $sql = "WITH abc AS (SELECT TeamID FROM project WHERE ProjectManagerID = ".$userID." AND ProjectID = ".$projectID.")"
                . " SELECT b.UserID, concat(b.FirstName,' ',b.LastName) AS fullName, IFNULL(SUM(e.Status),0) AS totalTasks FROM abc a"
                . " INNER JOIN existinguser b ON a.TeamID = b.TeamID"
                . " LEFT JOIN schedule c ON b.UserID = c.UserID"
                . " LEFT JOIN task d ON c.UserID = d.UserID"
                . " LEFT JOIN taskinfo e ON d.MainTaskID = e.MainTaskID"
                . " WHERE b.SpecialisationID = ".$specialisationID
                . " AND (b.Role = 'F' OR b.Role = 'P') AND b.Status = ".$userStatus
                . " AND c.WorkDate BETWEEN '".$startDate."' AND '".$endDate."'"
                . " GROUP BY b.UserID"
                . " ORDER BY totalTasks ASC";

            if (isset($_POST['numstaff'])) {
                
                $sql .= " LIMIT ".$numStaff.";";
            }

            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);

            //echo " ;; SQL = ".$sql;

            // auto allocation
            if(isset($_POST['numstaff'])) {

                if ($numStaffTeam >= $numStaff) {
                
                    // Insert into TaskInfo query
                    $stmt = $conn->prepare("INSERT INTO taskinfo (SpecialisationID,TaskName,TaskDesc,StartDate,DueDate,NumStaff,Priority,Status) VALUES (?,?,?,?,?,?,?,?)");

                    $stmt->bind_param("issssiii",$specialisationID,$taskName,$taskDesc,$startDate,$endDate,$numStaff,$priority,$taskStatus);

                    if ($stmt->execute()) {

                        $newMainTaskID = $stmt->insert_id;

                        // Insert into TaskInfo query
                        $sql = $conn->prepare("INSERT INTO task (MainTaskID,UserID) VALUES (?,?)");

                        foreach ($users as $user):

                            $sql->bind_param("ii",$newMainTaskID, $user['UserID']);

                            $sql->execute();

                            echo "<script type='text/javascript'>";
                            echo "alert('Task has been auto allocated.');";
                            echo "window.location = 'viewTasks.php';";
                            echo "</script>";
                        endforeach;

                    } else {
                        echo "FAILED! Error: " . $stmt->error;
                    }
                            
                } else {
                    echo "<script type='text/javascript'>";
                    echo "alert('The indicated number of staff with the specialisation needed for the task is more than what is available in the team');";
                    echo "window.location = 'addTask.php';";
                    echo "</script>";
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
                    $sql = $conn->prepare("INSERT INTO task (MainTaskID,UserID) VALUES (?,?)");
                    //echo "newMainTaskID = ".count($availStaff);

                    foreach ($selectStaff as $selectStaffID) {

                        $sql->bind_param("ii",$newMainTaskID, $selectStaffID);

                        $sql->execute();

                        echo "<script type='text/javascript'>";
                        echo "alert('Task has been allocated.');";
                        echo "window.location = 'viewTasks.php';";
                        echo "</script>";
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
        <img class="logo" src="tms.png">
    </div>

    <!-- Middle Section -->
    <div class="contentNav">
        
        <!-- Left Section (Navigation) -->
        <div class="navBar">
            <nav>
                <ul>
                <?php if ($employeeType == "Manager") { ?>
                    <li><a> &lt;name&gt;, Manager</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&manageaccount=true">Manage Account</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&taskmanagenent=true">Task Management</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&leavemanagenent=true">Leave Management</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&attendancemanagenent=true">Time/Attendance Tracking</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&newsfeedmanagenent=true">News Feed Management</a></li>
                    <li><a href="allHeadings.php?employeetype=Manager&projectmanagenent=true">Project Management</a></li>
					<li><a href="#">Logout</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
        
        <!-- Right Section (Activity) -->
        <div class="content">
            <h2 class="contentHeader">Select Staff for Task</h2>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addTask" action="addUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                        <input type="hidden" name="taskname" value="<?php echo $taskName; ?>">
                                        <input type="hidden" name="taskdesc" value="<?php echo $taskDesc; ?>">
                                        <input type="hidden" name="specialisationidname" value="<?php echo $specialisationIDName; ?>">
                                        <input type="hidden" name="startdate" value="<?php echo $startDate; ?>">
                                        <input type="hidden" name="enddate" value="<?php echo $endDate; ?>">
                                        <input type="hidden" name="priority" value="<?php echo $priority; ?>">
                                        <input type="hidden" name="numstaffteam" value="<?php echo $numStaffTeam; ?>">
                                        <input type="hidden" name="projectid" value="<?php echo $projectID; ?>">

                                        <?php
                                        if (isset($_GET['autoallocate']) && $_GET['autoallocate'] == 'on') { ?>

                                            <input type="number" id="numstaff" name="numstaff">

                                        <?php } else if ($isManual == 1){ ?>

                                            <label for="userid">Staff Name</label>

                                                <?php foreach ($users as $user):
                                                    echo "<div class='checkboxes'><input type='checkbox' name='selectStaff[]' value='". $user['UserID']."'>" . $user['fullName']."</option>";
                                                endforeach; ?>
                                            
                                        
                                        <?php } ?>
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
