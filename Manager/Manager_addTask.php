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

    $validSpecialisation = FALSE;
    $validSchedule = FALSE;
    $validDate = FALSE;

    $isManual = FALSE;


    // get project for the select option
    $sql = "SELECT MainProjectID, ProjectName FROM projectinfo"
        . " WHERE ProjectManagerID = ".$userID." AND CompanyID = ".$companyID." ORDER BY ProjectName ASC;";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);
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
            <div class="task-header">
                <h2>Allocate / Schedule Task</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="addTask" action="Manager_addUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="taskname">Task Name</label>
                                        <input type="text" id="taskname" name="taskname" required>
                                            
                                        <label for="taskdesc">Task Description</label>
                                        <textarea id="taskdesc" name="taskdesc" rows="6" required></textarea>

                                        <label for="project">Project</label>
                                        <select name='mainprojectid'>
                                            <?php
                                            foreach ($projects as $project):
                                                echo "<option value='". $project['MainProjectID']."'>". $project['ProjectName']."</option>";
                                            endforeach;
                                            ?>
                                        </select>
                                    
                                    </div>

                                    <div class="col-50">

                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority" required>
                                            <option value="3">Low</option>
                                            <option value="2">Mid</option>
                                            <option value="1">High</option>
                                        </select>
                                        
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" required>

                                        <label for="enddate">End Date</label>
                                        <input type="date" id="enddate" name="enddate" required>

                                        <!--<label for="allocate">Manual or Auto Allocate?</label>
                                        <select id="allocate" name="allocate" oninput="checkOption(this)">
                                            <option value="manual">Manual Allocation</option>
                                            <option value="auto">Auto Allocation</option>
                                        </select>-->

                                        <label>
                                            Auto Allocate <input type="checkbox" name="allocatetype" value="auto">
                                        </label>
                                    </div>
                                
                                </div>
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="addTask" type="submit" class="btn">Allocate to Staff</button>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>
