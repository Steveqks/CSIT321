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

    if (isset($_GET['maintaskid'])) {
        $mainTaskID = $_GET['maintaskid'];

        // FORM
        // get Task details
        $sql = "SELECT a.MainTaskID, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, e.MainProjectID, e.ProjectName
                FROM taskinfo a
                INNER JOIN task b ON a.MainTaskID = b.MainTaskID
                LEFT JOIN projectinfo e ON a.MainProjectID = e.MainProjectID
                WHERE a.MainTaskID = ".$mainTaskID."
                GROUP BY a.MainTaskID, a.TaskName, a.TaskDesc, a.StartDate, a.DueDate, a.Status, a.Priority, e.MainProjectID, e.ProjectName;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $taskDetails = $result->fetch_assoc();


        // get project for the select option
        $sql = "SELECT MainProjectID, ProjectName FROM projectinfo
                WHERE ProjectManagerID = ".$userID." AND CompanyID = ".$companyID."
                AND MainProjectID NOT IN (".$taskDetails['MainProjectID'].")
                ORDER BY ProjectName ASC;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);
    }


    if(isset($_POST['editTask'])) {

        $taskStatus = $_POST['statusid'];
        $mainTaskID = $_POST['maintaskid'];

        // Update into TaskInfo query
        $stmt = $conn->prepare("UPDATE taskinfo SET Status=? WHERE MainTaskID=?");

        $stmt->bind_param("ii",$taskStatus,$mainTaskID);

        if ($stmt->execute()) {

            header("Location: Manager_viewTask.php?message=Task status has been updated successfully.&maintaskid=".$mainTaskID);
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
            <div class="task-header">
                <h2>Edit Task</h2>
            </div>

            <div class="innerContent">

                <div class="row">
                    <div class="col-75">
                        <div class="container">

                            <?php
                            if (isset($_GET['changestatus'])) { ?>
                            <form name="editTask" action="Manager_editTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <input type="hidden" name="maintaskid" value="<?php echo $mainTaskID; ?>">

                                        <label for="status">Status</label>
                                        <select name="statusid">
                                            <?php
                                                if ($taskDetails['Status'] == 1) {

                                                    echo "<option value=1>Ongoing</option>
                                                        <option value=0>Done</option>";

                                                } else {

                                                    echo "<option value=0>Done</option>
                                                        <option value=1>Ongoing</option>";
                                                }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="editTask" type="submit" class="btn">Save</button>
                            </form>
                            <?php } else {
                            ?>

                            <form name="editTask" action="Manager_editUsersTask.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <input type="hidden" name="maintaskid" value="<?php echo $mainTaskID; ?>">
                                        

                                        <label for="taskname">Task Name</label>
                                        <input type="text" id="taskname" name="taskname" value="<?php echo $taskDetails['TaskName']; ?>">
                                            
                                        <label for="taskdesc">Task Description</label>
                                        <textarea id="taskdesc" name="taskdesc" rows="6"><?php echo $taskDetails['TaskDesc']; ?></textarea>

                                        <label for="project">Project</label>
                                        <select name="mainprojectid" required>

                                            <?php echo "<option value='". $taskDetails['MainProjectID']."'>".$taskDetails['ProjectName']."</option>"; ?>

                                            <?php foreach ($projects as $project):
                                                echo "<option value='". $project['MainProjectID']."'>" . $project['ProjectName']."</option>";
                                            endforeach; ?>

                                        </select>
                                        
                                        <label for="status">Status</label>
                                        <select name="statusid">
                                            <?php
                                                if ($taskDetails['Status'] == 1) {

                                                    echo "<option value=1>Ongoing</option>
                                                        <option value=0>Done</option>";

                                                } else {

                                                    echo "<option value=0>Done</option>
                                                        <option value=1>Ongoing</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-50">

                                        <label for="priority">Priority</label>
                                        <select id="priority" name="priority">
                                            <?php
                                                if ($taskDetails['Priority'] == 1) {

                                                    echo "<option value='1'>High</option>
                                                        <option value='3'>Low</option>
                                                        <option value='2'>Mid</option>";

                                                } else if ($taskDetails['Priority'] == 2) {

                                                    echo "<option value='2'>Mid</option>
                                                        <option value='3'>Low</option>
                                                        <option value='1'>High</option>";
                                                        
                                                } else {

                                                    echo "<option value='3'>Low</option>
                                                        <option value='2'>Mid</option>
                                                        <option value='1'>High</option>";
                                                        
                                                }
                                            ?>
                                        </select>
                                        
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" value="<?php echo $taskDetails['StartDate']; ?>">

                                        <label for="enddate">End Date</label><!--<p id="disableEnddate" style="color:red;"></p>-->
                                        <input type="date" id="enddate" name="enddate" value="<?php echo $taskDetails['DueDate']; ?>">

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

                                <button name="editTask" type="submit" class="btn">Allocate to Staff</button>
                                
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>