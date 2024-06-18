<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/header.css" />
    <link rel="stylesheet" href="./css/all.css" />

    <?php
        include 'db_connection.php';

        $conn = OpenCon();

        $userID = 1;
        $statusID = 1;
        $employeeType = "Manager";

        if ($employeeType == "Manager") {

            $sql = "WITH abc AS ("
                . " SELECT a.TeamID, b.SpecialisationID, COUNT(b.UserID) AS totalNumStaff FROM team a"
                . " INNER JOIN existinguser b ON a.TeamID = b.TeamID"
                . " WHERE a.ManagerID = ".$userID." GROUP BY a.TeamID, b.SpecialisationID"
                . ")"
                . " SELECT d.TaskName, d.StartDate, d.DueDate, d.NumStaff, a.totalNumStaff, c.MainTaskID, concat(b.FirstName, ' ', b.LastName) AS fullName, b.UserID FROM abc a"
                ." INNER JOIN existinguser b ON a.TeamID = b.TeamID"
                ." INNER JOIN task c ON b.UserID = c.UserID"
                ." INNER JOIN taskinfo d ON d.MainTaskID = c.MainTaskID"
                ." WHERE d.SpecialisationID = a.SpecialisationID AND d.Status = ".$statusID
                ." GROUP BY d.TaskName, d.NumStaff, d.StartDate, d.DueDate, d.MainTaskID, fullName;";
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
                        <th>Req Number of Staff</th>
                    <?php
                        }
                    ?>

                    </tr>

                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td>
                                <?php if ($employeeType == "Manager") { ?>
                                    <a href="editTask.php?maintaskid=<?php echo htmlspecialchars($task['MainTaskID']); ?>"><?php echo htmlspecialchars($task['TaskName']); ?></a>
                                <?php } else { ?>
                                    <?php echo htmlspecialchars($task['TaskName']); ?>
                                <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['StartDate']); ?></td>
                                <td><?php echo htmlspecialchars($task['DueDate']); ?></td>

                                <?php if ($employeeType == "Manager") { ?>
                                    <td><?php echo htmlspecialchars($task['fullName']); ?></td>
                                    <td><?php echo htmlspecialchars($task['NumStaff']); ?> / <?php echo htmlspecialchars($task['totalNumStaff']); ?></td>
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
