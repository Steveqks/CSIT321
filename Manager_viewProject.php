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

        $employeeType = "Manager";
        $userID = 2;

        // get teams for the team option in form
        $sql = "SELECT a.MainProjectID, a.ProjectName, concat(c.FirstName, ' ', c.LastName) AS fullName FROM projectinfo a
                INNER JOIN project d ON d.MainProjectID = a.MainProjectID
                LEFT JOIN teaminfo b ON d.MainTeamID = b.MainTeamID
                LEFT JOIN existinguser c ON b.ManagerID = c.UserID
                WHERE a.ProjectManagerID = ".$userID."
                GROUP BY a.MainProjectID, a.ProjectName;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_GET['mainprojectid'])) {
            $mainProjectID = $_GET['mainprojectid'];

            // Delete project
            $stmt = $conn->prepare("DELETE FROM project WHERE MainProjectID = ?");

            $stmt->bind_param("i",$mainProjectID);

            if ($stmt->execute()) {

                // Delete project
                $stmt = $conn->prepare("DELETE FROM projectinfo WHERE MainProjectID = ?");
    
                $stmt->bind_param("i",$mainProjectID);

                if ($stmt->execute()) {
                    echo "<script type='text/javascript'>";
                    echo "alert('Project has been deleted.');";
                    echo "window.location = 'viewProject.php';";
                    echo "</script>";
                }
            }
        }
        
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
            <div class="task-header">
                <i class="fas fa-user"></i>
                    <h2>View Projects</h2>
            </div>

            <div class="innerContent">
                <table class="tasks">

                    <tr>
                        <th>Project Name</th>
                        <th>Project Manager</th>
                        <th>Delete</th>
                    </tr>

                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <a href="editProject.php?mainprojectid=<?php echo $project['MainProjectID']; ?>"><?php echo $project['ProjectName']; ?></a>
                                </td>
                                <td><?php echo $project['fullName']; ?></td>
                                <td><a href="viewProject.php?mainprojectid=<?php echo $project['MainProjectID']; ?>">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No projects created.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
