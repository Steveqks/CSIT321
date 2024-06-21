<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackMySchedule</title>
    <link rel="stylesheet" href="./css/header.css" />
    <link rel="stylesheet" href="./css/all.css" />

    <?php
        $userID = 1;
        $employeeType = "Manager";
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
            <?php
            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['manageaccount']) == "true")) {
            ?>
                <h2 class="contentHeader">Manage Account</h2>
            <?php }

            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['taskmanagenent']) == "true")) {
            ?>
                <h2 class="contentHeader">Task Management</h2>
            <?php }

            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['leavemanagenent']) == "true")) {
            ?>
                <h2 class="contentHeader">Leave Management</h2>
            <?php }

            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['attendancemanagenent']) == "true")) {
            ?>
                <h2 class="contentHeader">Time/Attendance Tracking</h2>
            <?php }

            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['newsfeedmanagenent']) == "true")) {
            ?>
                <h2 class="contentHeader">News Feed Management</h2>
            <?php }

            if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['projectmanagenent']) == "true")) {
            ?>
                <h2 class="contentHeader">Project Management</h2>
            <?php } ?>


            <div class="innerContent">
                <?php
                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['manageaccount']) == "true")) {
                ?>

                <a href="createUserAccount.php"><button>Create User Account</button></a>
                <a href="editAccount.php"><button>Edit Account</button></a>

                <?php }
                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['taskmanagenent']) == "true")) {
                ?>

                <a href="viewTasks.php"><button>View Tasks</button></a>
                <a href="addTask.php"><button>Allocate Task</button></a>

                <?php }
                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['leavemanagenent']) == "true")) {
                ?>

                <a href="#"><button>Leave History</button></a>

                <?php }

                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['attendancemanagenent']) == "true")) {
                ?>
                    <a href="#"><button>View Time Management</button></a>
                <?php }

                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['newsfeedmanagenent']) == "true")) {
                ?>
                    <a href="#"><button>News Feed Management</button></a>
                <?php }

                if ((isset($_GET['employeetype']) == "Manager") && (isset($_GET['projectmanagenent']) == "true")) {
                ?>
                    <a href="addProject.php"><button>Create Project</button></a>
                    <a href="viewProject.php"><button>View Projects</button></a>
                <?php } ?>



            </div>
        </div>
    </div>
</body>
</html>