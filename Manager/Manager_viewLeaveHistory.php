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


        //echo $sql;

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
        <?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div class="content">
            <h2 class="contentHeader">View Tasks</h2>

            <div class="innerContent">

                <table class="tasks">

                    <tr>
                        <th>Task Name</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th>Assigned To</th>
                        <th>Avail / Req Number of Staff</th>
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
                            <td colspan="5">No tasks assigned.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
