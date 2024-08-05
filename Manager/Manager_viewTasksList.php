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

    $userStatusID = 1;
    $taskStatusID = 1;

    $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
            (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
            pi.ProjectName, ti.Status
            FROM projectinfo pi
            JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
            JOIN task t ON t.MainTaskID = ti.MainTaskID
            JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
            JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
            WHERE pi.ProjectManagerID = ".$userID."
            GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
            ORDER BY ti.Status DESC, ti.DueDate ASC;";

            //echo $sql;

    $stmt = $conn->prepare($sql);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
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
            <h2 class="contentHeader">View Tasks List</h2>

            <div class="innerContent">
                
                <?php
                    if (isset($_GET['message'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                    } elseif (isset($_GET['error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                ?>

                <table class="tasks">

                    <tr>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Assigned Date</th>
                        <th>Due Date</th>
                        <th>Avail / Req Number of Staff</th>
                        <th>Status</th>
                    </tr>

                    <?php if (count($tasks) > 0): ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?php echo $task['ProjectName']; ?></td>
                                <td>
                                    <a href="Manager_viewTask.php?maintaskid=<?php echo htmlspecialchars($task['MainTaskID']); ?>"><?php echo htmlspecialchars($task['TaskName']); ?></a>
                                </td>
                                <td><?php echo date('F j, Y',strtotime($task['StartDate'])); ?></td>
                                <td><?php echo date('F j, Y',strtotime($task['DueDate'])); ?></td>
                                <td><?php echo htmlspecialchars($task['totalNumStaff']); ?> / <?php echo htmlspecialchars($task['NumStaff']); ?></td>

                                <?php
                                    if ($task['Status'] == 1) {
                                        $statusName = "Ongoing";
                                    } else {
                                        $statusName = "Done";
                                    }
                                    echo "<td>".$statusName."</td>";
                                ?>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No tasks assigned.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
