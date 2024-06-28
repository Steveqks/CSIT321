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

        $firstName = $_SESSION['FirstName'];
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
            <?php
            if (isset($_GET['manageaccount']) == "true") {
            ?>
                <h2 class="contentHeader">Manage Account</h2>
            <?php }

            if (isset($_GET['taskmanagenent']) == "true") {
            ?>
                <h2 class="contentHeader">Task Management</h2>
            <?php }

            if (isset($_GET['leavemanagenent']) == "true") {
            ?>
                <h2 class="contentHeader">Leave Management</h2>
            <?php }

            if (isset($_GET['attendancemanagenent']) == "true") {
            ?>
                <h2 class="contentHeader">Time/Attendance Tracking</h2>
            <?php }

            if (isset($_GET['newsfeedmanagenent']) == "true") {
            ?>
                <h2 class="contentHeader">News Feed Management</h2>
            <?php }

            if (isset($_GET['projectmanagenent']) == "true") {
            ?>
                <h2 class="contentHeader">Project Management</h2>
            <?php } ?>


            <div class="innerContent">
                <?php
                if (isset($_GET['manageaccount']) == "true") {
                ?>

                <a href="Manager_createUserAccount.php"><button>Create User Account</button></a>
                <a href="Manager_editAccount.php"><button>Edit Account</button></a>

                <?php }
                if (isset($_GET['taskmanagenent']) == "true") {
                ?>

                <a href="Manager_viewTasks.php"><button>View Tasks</button></a>
                <a href="Manager_addTask.php"><button>Allocate Task</button></a>

                <?php }
                if (isset($_GET['leavemanagenent']) == "true") {
                ?>

                <a href="#"><button>Leave History</button></a>

                <?php }

                if (isset($_GET['attendancemanagenent']) == "true") {
                ?>
                    <a href="#"><button>View Time Management</button></a>
                <?php }

                if (isset($_GET['projectmanagenent']) == "true") {
                ?>
                    <a href="Manager_addProject.php"><button>Create Project</button></a>
                    <a href="Manager_viewProject.php"><button>View Projects</button></a>
                <?php } ?>



            </div>
        </div>
    </div>
</body>
</html>