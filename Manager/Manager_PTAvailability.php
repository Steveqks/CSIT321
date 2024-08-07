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
        include '../Session/session_check_user_Manager.php';

        $userID = $_SESSION['UserID'];
        $firstName = $_SESSION['FirstName'];
        $companyID = $_SESSION['CompanyID'];

        $showConfirmForm = FALSE;


        // Connect to the database
        $conn = OpenCon();

        // Function to get the date of a specific day from next Monday
        function getDateFromMonday($weekStartDate1, $day) {
            $weekStartDate2 = new DateTime($weekStartDate1);
            $date = clone $weekStartDate2;
            $dayOffset = ['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2, 'Thursday' => 3, 'Friday' => 4, 'Saturday' => 5, 'Sunday' => 6];
            return $date->modify("+{$dayOffset[$day]} days")->format('Y-m-d');
        }




        // Create a new DateTime object for today
        $today = new DateTime();

        // Modify the DateTime object to get the next Monday
        $nextMonday = clone $today;
        if ($today->format('N') != 1) {
            $nextMonday->modify('next monday');
        } else {
            // If today is Monday, we need to get the next Monday, not today
            $nextMonday->modify('+1 week');
        }

        $formatted_monday = $nextMonday->format('Y-m-d');

        $sql = "SELECT a.AvailabilityID, a.UserID, a.WeekStartDate, a.DayOfWeek, a.IsAvailable, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                FROM availability a
                LEFT JOIN existinguser b ON a.UserID = b.UserID
                WHERE a.IsAvailable = 1
                AND a.WeekStartDate >= '".$formatted_monday."'
                AND a.Status IS NULL
                GROUP BY a.UserID, a.WeekStartDate, a.DayOfWeek, a.IsAvailable, fullName
                ORDER BY a.WeekStartDate,
                    CASE
                        WHEN a.DayOfWeek = 'Monday' THEN 1
                        WHEN a.DayOfWeek = 'Tuesday' THEN 2
                        WHEN a.DayOfWeek = 'Wednesday' THEN 3
                        WHEN a.DayOfWeek = 'Thursday' THEN 4
                        WHEN a.DayOfWeek = 'Friday' THEN 5
                        WHEN a.DayOfWeek = 'Saturday' THEN 6
                        WHEN a.DayOfWeek = 'Sunday' THEN 7
                    END;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $availability = $result->fetch_all(MYSQLI_ASSOC);



        if (isset($_GET['approve']) && $_GET['approve'] == "yes") {

            $availabilityID = $_GET['availabilityid'];

            $showConfirmForm = TRUE;

            $sql = "SELECT a.AvailabilityID, a.UserID, a.WeekStartDate, a.DayOfWeek, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                    FROM availability a
                    LEFT JOIN existinguser b ON a.UserID = b.UserID
                    WHERE a.AvailabilityID = ".$availabilityID."
                    GROUP BY a.UserID, a.WeekStartDate, a.DayOfWeek, fullName;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $confirmAvail = $result->fetch_assoc();
            
        } else if (isset($_GET['approve']) && $_GET['approve'] == "no") {

            $availabilityID = $_GET['availabilityid'];
            
            $stmt = $conn->prepare("UPDATE availability SET Status=0 WHERE AvailabilityID=?");
    
            $stmt->bind_param("i",$availabilityID);

            if ($stmt->execute()) {
                header("Location: Manager_PTAvailability.php");
            }
        }


        if (isset($_POST['schedulePT'])) {

            $availabilityID = $_POST['availabilityid'];
            $availUserID = $_POST['availuserid'];
            $availDate = $_POST['availdate'];

            $startWork = $availDate." ".$_POST['startwork'];
            $endWork = $availDate." ".$_POST['endwork'];

            $stmt = $conn->prepare("UPDATE availability SET Status=1 WHERE AvailabilityID=?");

            $stmt->bind_param("i",$availabilityID);

            if ($stmt->execute()) {
            
                $stmt = $conn->prepare("INSERT INTO schedule (UserID,WorkDate,StartWork,EndWork) VALUES (?,?,?,?)");

                $stmt->bind_param("isss",$availUserID,$availDate,$startWork,$endWork);

                if ($stmt->execute()) {
                    header("Location: Manager_PTSchedule.php?message=Part-Time availability has been confirmed.");
                }
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
            <div class="task-header">
                <h2>Part-Time Availability</h2>

            <div class="innerContent">

                <?php
                if ($showConfirmForm) {
                ?>
                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form action="Manager_PTAvailability.php" method="POST">
                                <div class="row">
                                    <div class="col-50">

                                        <input type="hidden" name="availabilityid" value="<?php echo $availabilityID; ?>">
                                        <input type="hidden" name="availuserid" value="<?php echo $confirmAvail['UserID']; ?>">
                                        <input type="hidden" name="availdate" value="<?php echo getDateFromMonday($confirmAvail['WeekStartDate'], $confirmAvail['DayOfWeek']); ?>">

                                        <label for="fullName">Full Name</label>
                                        <input type="text" name="fullname" value="<?php echo $confirmAvail['fullName']; ?>" disabled>

                                        <label for="availdateformat">Available Date</label>
                                        <input type="text" name="availDateFormat" value="<?php echo date('F j, Y',strtotime(getDateFromMonday($confirmAvail['WeekStartDate'], $confirmAvail['DayOfWeek']))); ?>" disabled>
                                        
                                        <label for="dayofweek">Day</label>
                                        <input type="text" name="dayofweek" value="<?php echo $confirmAvail['DayOfWeek']; ?>" disabled>

                                    </div>
                                    <div class="col-50">

                                        <label for="startwork">Start Work</label>
                                        <input type="time" name="startwork" required>

                                        <label for="endwork">End Work</label>
                                        <input type="time" name="endwork" required>

                                    </div>
                                </div>

                                <button name="schedulePT" type="submit" class="btn">Save</button>

                            </form>
                        </div>
                    </div>
                </div>

                <?php
                } else {
                ?>

                <table class="tasks">

                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Confirm Schedule</th>
                    </tr>

                    <?php
                    if (count($availability) > 0) {
                        foreach ($availability as $avail) {

                            echo "<tr><td>".$avail['fullName']."</td>";

                            echo "<td>".date('F j, Y',strtotime(getDateFromMonday($avail['WeekStartDate'], $avail['DayOfWeek'])))."</td>";
                            
                            echo "<td>".$avail['DayOfWeek']."</td>";
                            echo "<td><a href='Manager_PTAvailability.php?approve=yes&availabilityid=".$avail['AvailabilityID']."'>Confirm</a>&emsp;<a href='#' onclick='return declineSchedule(".$avail['AvailabilityID'].");'>Decline</a></td></tr>";

                        }
                    } else {
                        echo "<tr><td colspan='4'>No upcoming availability found.</td></tr>";
                    }?>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">

    function declineSchedule(availabilityID) {

        let text = "Confirm to decline schedule?";

        if (confirm(text) == true) {
            window.location = "Manager_PTAvailability.php?approve=no&availabilityid=" + availabilityID;
        } else {
            window.location = "Manager_PTAvailability.php";
        }
    }
</script>
</html>