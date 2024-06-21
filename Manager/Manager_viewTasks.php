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

        $userStatusID = 1;
        $taskStatusID = 1;

        if ($employeeType == "Manager") {

            $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                    (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                    CONCAT(eu.FirstName, ' ', eu.LastName) AS fullName
                    FROM task t
                    JOIN taskinfo ti ON t.MainTaskID = ti.MainTaskID
                    JOIN existinguser eu ON t.UserID = eu.UserID
                    JOIN team te ON eu.UserID = te.UserID
                    JOIN teaminfo tei ON te.MainTeamID = tei.MainTeamID
                    WHERE tei.ManagerID = ".$userID."
                    ORDER BY ti.MainTaskID;";
/*
            // GET NUMBER OF USERS IN THE TEAM, GROUP BY SPECIALISATION
            $sql = "WITH abc AS ("
                . " SELECT a.MainTeamID, a.MainTaskID, b.SpecialisationID, COUNT(a.UserID) AS totalNumStaff FROM task a
                    INNER JOIN taskinfo b ON a.MainTaskID = b.MainTaskID
                    GROUP BY a.MainTeamID, a.MainTaskID, b.SpecialisationID"
                . ")"
            // GET NUMBER AND DETAILS OF USERS FROM THE TEAM THAT MATCHED THE TASK
                . " SELECT e.TaskName, e.StartDate, e.DueDate, e.NumStaff, a.totalNumStaff, d.MainTaskID, concat(c.FirstName, ' ', c.LastName) AS fullName, c.UserID FROM abc a"
                . " INNER JOIN team b ON a.MainTeamID = b.MainTeamID"
                . " INNER JOIN existinguser c ON c.UserID = b.UserID"
                . " INNER JOIN task d ON c.UserID = d.UserID"
                . " INNER JOIN taskinfo e ON e.MainTaskID = d.MainTaskID"
                . " WHERE e.SpecialisationID = a.SpecialisationID AND e.Status = ".$taskStatusID
                . " AND c.Status = ".$userStatusID
                . " GROUP BY e.TaskName, e.NumStaff,a.totalNumStaff, e.StartDate, e.DueDate, e.MainTaskID, fullName;";
*/
                //echo $sql;
        } else {
            $sql = "SELECT a.TaskName, a.StartDate, a.DueDate FROM taskinfo a inner join task b on a.MainTaskID = b.MainTaskID WHERE b.UserID = 4";
        }

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
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
            <h2 class="contentHeader">View Tasks</h2>

            <div class="innerContent">

                <table class="tasks">

                    <tr>
                        <th>Task Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                    <?php
                        if ($employeeType == "Manager") {
                    ?>
                        <th>Assigned To</th>
                        <th>Avail / Req Number of Staff</th>
                    <?php
                        }
                    ?>

                    </tr>

                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td>
                                <?php if ($employeeType == "Manager") { ?>
                                    <a href="Manager_editTask.php?maintaskid=<?php echo htmlspecialchars($task['MainTaskID']); ?>"><?php echo htmlspecialchars($task['TaskName']); ?></a>
                                <?php } else { ?>
                                    <?php echo htmlspecialchars($task['TaskName']); ?>
                                <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($task['DueDate']); ?></td>

                                <?php if ($employeeType == "Manager") { ?>
                                    <td><?php echo htmlspecialchars($task['fullName']); ?></td>
                                    <td><?php echo htmlspecialchars($task['totalNumStaff']); ?> / <?php echo htmlspecialchars($task['NumStaff']); ?></td>
                                <?php } ?>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No tasks assigned.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
