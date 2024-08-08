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

        // Connect to the database
        $conn = OpenCon();


        if (isset($_POST['search'])) {
        
            if ($_POST['searchDate'] === "" && $_POST['searchInput'] === "") {
    
                header('Location: Manager_PTSchedule.php?searcherror=Please key in date or name to search.');
    
            } else {
    
                if (isset($_POST['searchDate']) && $_POST['searchDate'] != "") {
                    $searchDate = $_POST['searchDate'];
                }
    
                if (isset($_POST['searchInput']) && $_POST['searchInput'] != "") {

                    $searchInput = explode(" ", $_POST['searchInput']);

                    $name1=""; $name2=""; $name3=""; $name4=""; $name5="";

                    for ($i = 0; $i < count($searchInput); $i++) {
                        $name[$i] = $searchInput[$i];
                    }
                }
    
                if ($_POST['searchDate'] != "" && $_POST['searchInput'] != "") {
    
                    // Fetch schedule for the user starting from today
                    $sql = "SELECT a.WorkDate, a.StartWork, a.EndWork, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                            FROM schedule a
                            LEFT JOIN existinguser b ON a.UserID = b.UserID
                            WHERE (a.WorkDate = '".$searchDate."')";

                    for ($i = 0; $i < count($searchInput); $i++) {
                        $sql .= " AND (b.FirstName LIKE '%".$name[$i]."%' OR b.LastName LIKE '%".$name[$i]."%')";
                    }
                            
                    $sql .= "ORDER BY fullName ASC;";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $schedule = $result->fetch_all(MYSQLI_ASSOC);
    
                } else if ($_POST['searchDate'] != "") {
    
                    // Fetch schedule for the user starting from today
                    $sql = "SELECT a.WorkDate, a.StartWork, a.EndWork, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                            FROM schedule a
                            LEFT JOIN existinguser b ON a.UserID = b.UserID
                            WHERE (a.WorkDate = '".$searchDate."')
                            ORDER BY a.WorkDate ASC;";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $schedule = $result->fetch_all(MYSQLI_ASSOC);
    
                } else if ($_POST['searchInput'] != "") {
    
                    // Fetch schedule for the user starting from today
                    $sql = "SELECT a.WorkDate, a.StartWork, a.EndWork, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                            FROM schedule a
                            LEFT JOIN existinguser b ON a.UserID = b.UserID
                            WHERE a.WorkDate >= CURDATE()";

                    for ($i = 0; $i < count($searchInput); $i++) {
                        $sql .= " AND (b.FirstName LIKE '%".$name[$i]."%' OR b.LastName LIKE '%".$name[$i]."%')";
                    }
                    
                    $sql .= "ORDER BY a.WorkDate ASC;";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $schedule = $result->fetch_all(MYSQLI_ASSOC);
    
                }
    
            }
        } else {
    
            // Fetch schedule for the user starting from today
            $sql = "SELECT a.WorkDate, a.StartWork, a.EndWork, CONCAT(b.FirstName, ' ', b.LastName) AS fullName
                    FROM schedule a
                    LEFT JOIN existinguser b ON a.UserID = b.UserID
                    WHERE a.WorkDate >= CURDATE()
                    ORDER BY a.WorkDate ASC;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedule = $result->fetch_all(MYSQLI_ASSOC);

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
                <h2>Part-Time Schedule</h2>
            </div>
            
            <div class="search">
                <form action="Manager_PTSchedule.php" method="POST">
                    <label for="search">Search
                    <span>Date: <input type="date" name="searchDate"></span>
                    <span>Name: <input type="text" name="searchInput" placeholder="Enter name"></span>
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
                        <th>Name</th>
                        <th>Work Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>

                    <?php
                    if (count($schedule) > 0) {
                        foreach ($schedule as $shift) {

                            echo "<tr><td>".$shift['fullName']."</td>";
                            echo "<td>".date('F j, Y',strtotime($shift['WorkDate']))."</td>";
                            echo "<td>".date('h:ia',strtotime($shift['StartWork']))."</td>";
                            echo "<td>".date('h:ia',strtotime($shift['EndWork']))."</td></tr>";

                        }
                    } else {
                        echo "<tr><td colspan='4'>No upcoming schedules found.</td></tr>";
                    }?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>