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

    if (isset($_GET['search'])) {

        $searchInput = $_GET['searchInput'];

        $sql = "SELECT a.LeaveID, b.UserID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status
                FROM leaves a
                INNER JOIN existinguser b ON a.UserID = b.UserID
                WHERE b.Status = ".$userStatusID."
                AND (b.FirstName LIKE '%".$searchInput."%' OR b.LastName LIKE '%".$searchInput."%')
                GROUP BY a.LeaveID, b.UserID, fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status
                ORDER BY a.Status ASC, a.EndDate ASC;";


        //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $leaves = $result->fetch_all(MYSQLI_ASSOC);

    } else {

        $sql = "SELECT a.LeaveID, b.UserID, CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status
                FROM leaves a
                INNER JOIN existinguser b ON a.UserID = b.UserID
                WHERE b.Status = ".$userStatusID."
                GROUP BY a.LeaveID, b.UserID, fullName, a.StartDate, a.EndDate, a.LeaveType, a.HalfDay, a.Status
                ORDER BY a.Status ASC, a.EndDate ASC;";


        //echo $sql;

        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $leaves = $result->fetch_all(MYSQLI_ASSOC);

    }
    
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
            <h2 class="contentHeader">View Leave History</h2>
            
            <div class="search">
                <form action="Manager_viewLeaveHistory.php">
                    <input type="text" name="searchInput" placeholder="Enter name" required>
                    <input type="submit" class="searchBtn" name="search" value="Search">
                </form>
            </div>

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
                                    <?php echo date('F j, Y',strtotime($leave['StartDate'])); ?>
                                </td>
                                <td>
                                    <?php echo date('F j, Y',strtotime($leave['EndDate'])); ?>
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

                                            echo "<a href='#' onclick='return confirmApprove(".$leave['LeaveID'].");'>Approve</a>&emsp;";
                                            echo "<a href='#' onclick='return confirmDecline(".$leave['LeaveID'].");'>Decline</a>";

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

<script type="text/javascript">
    function confirmApprove(leaveID) {

        let text = "Confirm to approve leave?";
        
        if (confirm(text) == true) {
            window.location = "Manager_viewLeaveHistory.php?approve=yes&leaveid=" + leaveID;
        } else {
            window.location = "Manager_viewLeaveHistory.php";
        }
    }
    
    function confirmDecline(leaveID) {

        let text = "Confirm to decline leave?";

        if (confirm(text) == true) {
            window.location = "Manager_viewLeaveHistory.php?approve=no&leaveid=" + leaveID;
        } else {
            window.location = "Manager_viewLeaveHistory.php";
        }
    }
</script>
</html>
