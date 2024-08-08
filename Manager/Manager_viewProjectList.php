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

            header('Location: Manager_viewProjectList.php?searcherror=Please key in date or name to search.');

        } else {

            if (isset($_POST['searchDate'])) {
                $searchDate = $_POST['searchDate'];
            }

            if (isset($_POST['searchInput'])) {
                $searchInput = $_POST['searchInput'];
            }

            if ($_POST['searchDate'] != "" && $_POST['searchInput'] != "") {

                $sql = "SELECT * FROM projectinfo
                        WHERE ProjectManagerID = ".$userID."
                        AND EndDate > CURRENT_TIMESTAMP
                        AND (StartDate = ".$searchDate." OR EndDate = ".$searchDate.")
                        AND ProjectName LIKE '%".$searchInput."%'
                        ORDER BY ProjectName ASC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $projects = $result->fetch_all(MYSQLI_ASSOC);

            } else if ($_POST['searchDate'] != "") {

                $sql = "SELECT * FROM projectinfo
                        WHERE ProjectManagerID = ".$userID."
                        AND EndDate > CURRENT_TIMESTAMP
                        AND (StartDate = '".$searchDate."' OR EndDate = '".$searchDate."')
                        ORDER BY ProjectName ASC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $projects = $result->fetch_all(MYSQLI_ASSOC);

            } else if ($_POST['searchInput'] != "") {

                $sql = "SELECT * FROM projectinfo
                        WHERE ProjectManagerID = ".$userID."
                        AND EndDate > CURRENT_TIMESTAMP
                        AND ProjectName LIKE '%".$searchInput."%'
                        ORDER BY ProjectName ASC;";
    
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $projects = $result->fetch_all(MYSQLI_ASSOC);

            }

        }
    } else {

        $sql = "SELECT * FROM projectinfo
                WHERE ProjectManagerID = ".$userID."
                AND EndDate > CURRENT_TIMESTAMP
                ORDER BY EndDate ASC;";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = $result->fetch_all(MYSQLI_ASSOC);

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
                <h2>View Projects List</h2>
            </div>
            
            <div class="search">
                <form action="Manager_viewProjectList.php" method="POST">
                    <label for="search">Search
                    <span>Date: <input type="date" name="searchDate"></span>
                    <span>Project Name: <input type="text" name="searchInput" placeholder="Enter name"></span>
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
                        <th>Project Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>

                    <?php if (count($projects) > 0): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <a href="Manager_viewProject.php?mainprojectid=<?php echo $project['MainProjectID']; ?>"><?php echo $project['ProjectName']; ?></a>
                                </td>
                                <td><?php echo date('F j, Y',strtotime($project['StartDate'])); ?></td>
                                <td><?php echo date('F j, Y',strtotime($project['EndDate'])); ?></td>
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