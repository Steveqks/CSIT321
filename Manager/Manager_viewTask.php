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

        $taskStatus = 1;
        $userStatus = 1;


        if (isset($_GET['maintaskid'])) {
            $mainTaskID = $_GET['maintaskid'];
        
            // get task detail of the specific task
            $sql = "SELECT a.MainTaskID, a.SpecialisationID, e.SpecialisationName, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, f.MainTeamID, f.TeamName
                    FROM taskinfo a
                    INNER JOIN task b ON a.MainTaskID = b.MainTaskID
                    INNER JOIN teaminfo f ON f.MainTeamID = b.MainTeamID
                    INNER JOIN existinguser c ON b.UserID = c.UserID
                    INNER JOIN specialisation e ON e.SpecialisationID = a.SpecialisationID
                    WHERE a.MainTaskID = ".$mainTaskID."
                    GROUP BY a.MainTaskID, a.SpecialisationID, e.SpecialisationName, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, f.MainTeamID, f.TeamName;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $taskDetails = $result->fetch_assoc();

            $sql = "SELECT a.MainTaskID, a.UserID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                    FROM task a
                    INNER JOIN existinguser b ON a.UserID = b.UserID
                    WHERE a.MainTaskID = ".$mainTaskID."
                    GROUP BY a.MainTaskID, a.UserID, fullName;";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $usersTask = $result->fetch_all(MYSQLI_ASSOC);
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
                <h2>View Task</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <div class="row">
                                <?php if (isset($_GET['maintaskid'])) { ?>
                                    <div class="col-50">

                                        <span class="details">Task Name</span>
                                        <p><?php echo $taskDetails['TaskName']; ?></p>
                                            
                                        <span class="details">Task Description</span>
                                        <p><?php echo $taskDetails['TaskDesc']; ?></p>
                                        
                                        <span class="details">Specialisation</span>
                                        <p><?php echo $taskDetails['SpecialisationName']; ?></p>

                                        <span class="details">Team</span>
                                        <p><?php echo $taskDetails['TeamName']; ?></p>

                                        <span class="details">Staff Involved</span>
                                        <?php foreach ($usersTask as $taskDetail): echo "<p>".$taskDetail['fullName']."</p>"; endforeach; ?>

                                    </div>
                                    <div class="col-50">

                                        <span class="details">Priority</span>
                                        <p>
                                        <?php
                                            if ($taskDetails['Priority'] == 1) {
                                                $priorityName = "High";
                                            } else if ($taskDetails['Priority'] == 2) {
                                                $priorityName = "Mid";
                                            } else {
                                                $priorityName = "Low";
                                            }

                                            echo $priorityName;
                                        ?>
                                        </p>

                                        <span class="details">Start Date</span>
                                        <p><?php echo date('F j, Y',strtotime($taskDetails['StartDate'])); ?></p>

                                        <span class="details">End Date</span>
                                        <p><?php echo date('F j, Y',strtotime($taskDetails['DueDate'])); ?></p>
                                        
                                        <span class="details">Status</span>
                                        <p>
                                        <?php
                                            if ($taskDetails['Status'] == 1) {
                                                $statusName = "Ongoing";
                                            } else {
                                                $statusName = "Done";
                                            }
                                            echo $statusName;
                                        ?>
                                        </p>
                                    </div>
                                    <?php } ?>
                                
                                </div>

                                <a href="Manager_editTask.php?maintaskid=<?php echo $taskDetails['MainTaskID']; ?>" ><button name="editTask" class="btn">Edit</button></a>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>