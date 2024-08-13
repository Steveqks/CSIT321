<?php
    session_start();

    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];

    // Connect to the database
    $conn = OpenCon();

    if (isset($_GET['mainprojectid'])) {

        $mainProjectID = $_GET['mainprojectid'];

        // get project details
        $sql = "SELECT a.ProjectName, a.StartDate, a.EndDate FROM projectinfo a
                INNER JOIN project d ON d.MainProjectID = a.MainProjectID
                WHERE a.MainProjectID = ".$mainProjectID."
                GROUP BY a.MainProjectID, a.ProjectName, a.StartDate, a.EndDate;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $projectDetails = $result->fetch_all(MYSQLI_ASSOC);

    
        // get group project details
        $sql = "SELECT b.SpecialisationID, b.SpecialisationName FROM specialisationgroupinfo a
                INNER JOIN project d ON d.MainGroupID = a.MainGroupID
                LEFT JOIN specialisation b ON a.SpecialisationID = b.SpecialisationID
                WHERE d.MainProjectID = ".$mainProjectID."
                GROUP BY b.SpecialisationID, b.SpecialisationName;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $groupProjectDetails = $result->fetch_all(MYSQLI_ASSOC);

    
        // get Specialisation for the select option
        $sql = "SELECT * FROM specialisation WHERE CompanyID = ".$companyID
            . " AND SpecialisationID NOT IN (".$groupProjectDetails[0]['SpecialisationID'];

        if (count($groupProjectDetails) > 1) {
            for ($i = 1; $i < count($groupProjectDetails); $i++) {
                $sql .= ", ".$groupProjectDetails[$i]['SpecialisationID'];
            }
        }

        $sql .= ") ORDER BY SpecialisationName ASC;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $specialisations = $result->fetch_all(MYSQLI_ASSOC);

        // Close the statement and connection
        $stmt->close();
        CloseCon($conn);
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
            <h2>Edit Project</h2>

            <div class="innerContent">
                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editProject" action="Manager_editProjectGroup.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">

                                    
                                        <input type="hidden" name="mainprojectid" value="<?php echo $mainProjectID; ?>">

                                        <label for="projectname">Project Name</label>
                                        <input type="text" id="projectname" name="projectname" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['ProjectName']; endforeach; ?>">

                                        <label for="specialisationgroup">Group</label>
                                        <div class="checkbox-container">
                                            <?php
                                            foreach ($groupProjectDetails as $projectGroup):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='specialisation[]' value='". $projectGroup['SpecialisationID']."' checked>". $projectGroup['SpecialisationName']."</div>";
                                            endforeach;

                                            foreach ($specialisations as $specialisation):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='specialisation[]' value='". $specialisation['SpecialisationID']."'>". $specialisation['SpecialisationName']."</div>";
                                            endforeach;
                                            ?>
                                        </div>
                                            
                                        <label for="startdate">Start Date</label>
                                        <input type="date" id="startdate" name="startdate" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['StartDate']; endforeach; ?>">

                                        <label for="enddate">End Date</label>
                                        <input type="date" id="enddate" name="enddate" value="<?php foreach ($projectDetails as $projectDetail): echo $projectDetail['EndDate']; endforeach; ?>">
                                        
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>
                                </div>

                                <button name="editProject" type="submit" class="btn">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>