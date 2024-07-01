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

        $sql = "SELECT a.LeaveID, b.UserID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status
                FROM leaves a
                INNER JOIN team c ON a.UserID = c.UserID
                INNER JOIN teaminfo d ON c.MainTeamID = d.MainTeamID
                LEFT JOIN existinguser b ON c.UserID = b.UserID
                WHERE d.ManagerID = ".$userID."
                AND b.Status = ".$userStatusID."
                GROUP BY a.LeaveID, b.UserID, fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status;";


        //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $leaves = $result->fetch_all(MYSQLI_ASSOC);
        
        if (isset($_GET['approve'])) {

            $leaveID = $_GET['leaveid'];

            if ($_GET['approve'] == "yes") {

                $leaveStatus = 1;

            } else if ($_GET['approve'] == "no") {

                $leaveStatus = 0;

            }

            $stmt = $conn->prepare("UPDATE leaves SET Status=? WHERE LeaveID=?");

            $stmt->bind_param("ii",$leaveStatus,$leaveID);

            if ($stmt->execute()) {
                header('Location: Manager_viewLeaveHistory.php');
            }
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
            <h2 class="contentHeader">View Leave History</h2>

            <div class="innerContent">

                <table class="tasks">

                    <tr>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Half Day</th>
                        <th>Leave Type</th>
                        <th>Status</th>
                    </tr>

                    <?php if (count($leaves) > 0): ?>
                        <?php foreach ($leaves as $leave): ?>
                            <tr>
                                <td>
                                    <?php echo $leave['fullName']; ?>
                                </td>
                                <td>
                                    <?php echo $leave['StartDate']; ?>
                                </td>
                                <td>
                                    <?php echo $leave['EndDate']; ?>
                                </td>
                                <td>
                                    <?php
                                        if ($leave['HalfDay'] == 1) {
                                            echo "Yes";
                                        } else {
                                            echo "No";
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $leave['LeaveType']; ?>
                                </td>
                                <td>
                                    <?php
                                        if ($leave['Status'] === NULL) {

                                            echo "<a href='Manager_viewLeaveHistory.php?approve=yes&leaveid=".$leave['LeaveID']."'>Approve</a>&emsp;";
                                            echo "<a href='Manager_viewLeaveHistory.php?approve=no&leaveid=".$leave['LeaveID']."'>Decline</a>";

                                        } else if ($leave['Status'] !== NULL) {

                                            if ($leave['Status'] == 1) {
                                                echo "Approved";
                                            } else {
                                                echo "Declined";
                                            }
                                        }
                                    ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No leave history or applications.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
