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


    // SEARCH
    if (isset($_POST['search'])) {
        
        if ($_POST['searchDate'] === "" && $_POST['searchInput'] === "") {

            header('Location: Manager_viewTasksList.php?searcherror=Please key in date or name to search.');

        } else {

            if (isset($_POST['searchDate'])) {
                $searchDate = $_POST['searchDate'];
            }

            if (isset($_POST['searchInput'])) {
                $searchInput = $_POST['searchInput'];
            }

            if ($_POST['searchDate'] != "" && $_POST['searchInput'] != "") {

                $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                        (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                        pi.ProjectName, ti.Status
                        FROM projectinfo pi
                        JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
                        JOIN task t ON t.MainTaskID = ti.MainTaskID
                        JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
                        JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
                        WHERE pi.ProjectManagerID = ".$userID."
                        AND (ti.StartDate = '".$searchDate."' OR a.DueDate = '".$searchDate."')
                        AND pi.ProjectName LIKE '%".$searchInput."%'
                        GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
                        ORDER BY ti.Status DESC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $tasks = $result->fetch_all(MYSQLI_ASSOC);

            } else if ($_POST['searchDate'] != "") {

                $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                        (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                        pi.ProjectName, ti.Status
                        FROM projectinfo pi
                        JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
                        JOIN task t ON t.MainTaskID = ti.MainTaskID
                        JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
                        JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
                        WHERE pi.ProjectManagerID = ".$userID."
                        AND (ti.StartDate = '".$searchDate."' OR a.DueDate = '".$searchDate."')
                        GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
                        ORDER BY ti.Status DESC, pi.ProjectName ASC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $tasks = $result->fetch_all(MYSQLI_ASSOC);

            } else if ($_POST['searchInput'] != "") {

                $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                        (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                        pi.ProjectName, ti.Status
                        FROM projectinfo pi
                        JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
                        JOIN task t ON t.MainTaskID = ti.MainTaskID
                        JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
                        JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
                        WHERE pi.ProjectManagerID = ".$userID."
                        AND pi.ProjectName LIKE '%".$searchInput."%'
                        GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
                        ORDER BY ti.Status DESC, ti.DueDate, pi.ProjectName ASC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $tasks = $result->fetch_all(MYSQLI_ASSOC);

            }

        }
    } else if (isset($_GET['viewOngoingTasks'])) {

        $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                pi.ProjectName, ti.Status
                FROM projectinfo pi
                JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
                JOIN task t ON t.MainTaskID = ti.MainTaskID
                JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
                JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
                WHERE pi.ProjectManagerID = ".$userID."
                AND ti.Status = 1
                GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
                ORDER BY pi.ProjectName, ti.DueDate ASC;";

                //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);

    } else if (isset($_GET['viewCompletedTasks'])) {

        $sql = "SELECT ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff,
                (SELECT COUNT(*) FROM task WHERE MainTaskID = ti.MainTaskID) AS totalNumStaff,
                pi.ProjectName, ti.Status
                FROM projectinfo pi
                JOIN taskinfo ti ON pi.MainProjectID = ti.MainProjectID
                JOIN task t ON t.MainTaskID = ti.MainTaskID
                JOIN specialisationgroup sg ON sg.MainGroupID = t.MainGroupID
                JOIN specialisationgroupinfo sgi ON sgi.MainGroupID = sg.MainGroupID
                WHERE pi.ProjectManagerID = ".$userID."
                AND ti.Status = 0
                GROUP BY ti.MainTaskID, ti.TaskName, ti.StartDate, ti.DueDate, ti.NumStaff, ti.Status, sgi.GroupName
                ORDER BY pi.ProjectName, ti.DueDate ASC;";

                //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);

    } else {

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
                ORDER BY ti.Status DESC, pi.ProjectName, ti.DueDate ASC;";

                //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);

    }



    

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

            <div class="categories">
                <label for="categories">View By:
                    <a href='Manager_viewTasksList?viewOngoingTasks=true'><button>Ongoing Tasks</button></a>
                    <a href='Manager_viewTasksList?viewCompletedTasks=true'><button>Completed Tasks</button></a>
                </label>
            </div>
            
            <div class="search">
                <form action="Manager_viewTasksList.php" method="POST">
                    <label for="search">Search
                    <span>Date: <input type="date" name="searchDate"></span>
                    <span>Project Name: <input type="text" name="searchInput" placeholder="Enter name"></span>
                    <input type="submit" class="searchBtn" name="search" value="Search"></label>
                </form>
                                
                <?php
                    if (isset($_GET['searcherror'])) {
                        echo '<div class="searcherror-message">' . htmlspecialchars($_GET['searcherror']) . '</div>';
                    }
                ?>
            </div>

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
