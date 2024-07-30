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

    if (isset($_POST['editProject'])) {

        if ($_POST['startdate'] < $_POST['enddate']) {

            $projectName = $_POST['projectname'];
            $specialisation = $_POST['specialisation'];
            $startDate = $_POST['startdate'];
            $endDate = $_POST['enddate'];
            $mainProjectID = $_POST['mainprojectid'];


            // Check whether the Project Name that the Manager is in charge of exist
            $sql = "SELECT ProjectName FROM projectinfo
                    WHERE ProjectName LIKE '%".$projectName."%'
                    AND MainProjectID != ".$mainProjectID."
                    AND ProjectManagerID = ".$userID;

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $projectExist = $result->fetch_all(MYSQLI_ASSOC);


            $noOfProjectExist = 0;

            foreach ($projectExist as $projectExists) {

                if ($projectExists['ProjectName']) {
                    $noOfProjectExist = ++$noOfProjectExist;
                }
            }

            if ($noOfProjectExist > 0) {

                header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&error=Project Name with ".$projectName." already exist.");
                exit();

            } else {

                // Check if the selected Specialisation exist in Specialisation Group
                $sql = "SELECT a.SpecialisationID, a.SpecialisationName, IFNULL(b.SpecialisationID,0) AS noOfSpecialisation FROM specialisation a
                        LEFT JOIN specialisationgroupinfo b ON a.SpecialisationID = b.SpecialisationID
                        WHERE a.SpecialisationID IN (".$specialisation[0];
                
                if (count($specialisation) > 1) {
                    for ($i = 0; $i < count($specialisation); $i++) {

                        $sql .= ", " .$specialisation[$i];
                        
                    }
                }

                $sql .= ") GROUP BY a.SpecialisationID, a.SpecialisationName;";

                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                $specialisationExist = $result->fetch_all(MYSQLI_ASSOC);
                
                $noOfSpExist = 0;
                $spExistName = "";

                for ($i = 0; $i < count($specialisationExist); $i++) {

                    if ($specialisationExist[$i]['noOfSpecialisation'] == 0) {

                        $noOfSpExist = ++$noOfSpExist;
                        $spExistName .= " ".$specialisationExist[$i]['SpecialisationName'];

                    }
                }

                if ($noOfSpExist > 0) {

                    header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&error=No specialisation group with".$spExistName.". Please contact your Company Admin.");
                    exit();

                } else {

                    // Check if there are staff in the SpecialisationGroup
                    $sql = "SELECT a.GroupName, a.MainGroupID, IFNULL(b.MainGroupID,0) AS noOfSpGroupExist FROM specialisationgroupinfo a
                            LEFT JOIN specialisationgroup b ON a.MainGroupID = b.MainGroupID
                            WHERE a.CompanyID = ".$companyID
                        . " AND a.SpecialisationID IN (".$specialisation[0];

                    if (count($specialisation) > 1) {
                        
                        for ($i = 1; $i < count($specialisation); $i++) {
                            $sql .= ", ".$specialisation[$i];
                        }
                    }

                    $sql .= ");";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $spGroupsExists = $result->fetch_all(MYSQLI_ASSOC);

                    $stmt->close();

                    $noOfspGroupsExist = 0;
                    $spNotExistName = "";

                    foreach ($spGroupsExists as $spGroupsExist) {

                        if ($spGroupsExist['noOfSpGroupExist'] == 0) {
                            $noOfspGroupsExist = ++$noOfspGroupsExist;
                            $spNotExistName .= " ".$spGroupsExist['GroupName'];
                        }
                    }

                    if ($noOfspGroupsExist > 0) {

                        header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&error=No staff in the specialisation group with".$spNotExistName.". Please contact your Company Admin.");
                        exit();

                    } else {

                        // get SpecialisationGroup of the existing Project for the select option
                        $sql = "SELECT a.GroupName, a.MainGroupID, a.SpecialisationID FROM specialisationgroupinfo a
                                INNER JOIN project b ON a.MainGroupID = b.MainGroupID
                                WHERE a.CompanyID = ".$companyID
                            . " AND b.MainProjectID = ".$mainProjectID
                            . " AND a.SpecialisationID IN (".$specialisation[0];

                        if (count($specialisation) > 1) {
                            
                            for ($i = 1; $i < count($specialisation); $i++) {
                                $sql .= ", ".$specialisation[$i];
                            }
                        }

                        $sql .= ") ORDER BY a.GroupName ASC;";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $spGroupsProject = $result->fetch_all(MYSQLI_ASSOC);

                        $stmt->close();


                        // get SpecialisationGroup that does not belong to the existing Project for the select option
                        $sql = "WITH abc AS (SELECT a.GroupName, a.MainGroupID, a.SpecialisationID FROM specialisationgroupinfo a
                                INNER JOIN specialisationgroup b ON a.MainGroupID = b.MainGroupID
                                WHERE a.CompanyID = ".$companyID
                            . " AND a.SpecialisationID IN (".$specialisation[0];

                        if (count($specialisation) > 1) {
                            
                            for ($i = 1; $i < count($specialisation); $i++) {
                                $sql .= ", ".$specialisation[$i];
                            }
                        }
                        
                        $sql .= "))
                                SELECT * FROM abc
                                WHERE SpecialisationID NOT IN (".$spGroupsProject[0]['SpecialisationID'];

                        if (count($spGroupsProject) > 1) {
                            
                            for ($i = 1; $i < count($spGroupsProject); $i++) {
                                $sql .= ", ".$spGroupsProject[$i]['SpecialisationID'];
                            }
                        }

                        $sql .= ") ORDER BY GroupName ASC;";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $spGroups = $result->fetch_all(MYSQLI_ASSOC);

                        $stmt->close();

                        if (count($spGroups) < 0) {
                            header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&error=No staff in the specialisation group with".$spExistName.". Please contact your Company Admin.");
                            exit();
                        }
                    }
                }
                
            }
        } else {
            header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&error=Invalid date. Please make sure the Start Date is not more than the End Date.");
            exit();
        }
    }

    if (isset($_POST['editProjectGroup'])) {

        $projectName = $_POST['projectname'];
        $startDate = $_POST['startdate'];
        $endDate = $_POST['enddate'];
        $spGroup = $_POST['spgroup'];
        $mainProjectID = $_POST['mainprojectid'];

    
        $stmt = $conn->prepare("UPDATE projectinfo SET ProjectManagerID=?,CompanyID=?,ProjectName=?,StartDate=?,EndDate=? WHERE MainProjectID=?");

        $stmt->bind_param("iisssi",$userID,$companyID,$projectName,$startDate,$endDate,$mainProjectID);

        if ($stmt->execute()) {

            $stmt = $conn->prepare("DELETE FROM project WHERE MainProjectID = ?");

            $stmt->bind_param("i",$mainProjectID);

            if ($stmt->execute()) {

                $stmt = $conn->prepare("INSERT INTO project (MainProjectID,MainGroupID) VALUES (?,?)");

                foreach ($spGroup as $spGroupID) {

                    $stmt->bind_param("ii",$mainProjectID,$spGroupID);

                    $stmt->execute();

                    header("Location: Manager_editProject.php?mainprojectid=".$mainProjectID."&message=Project has been updated successfully.");

                }
                
            }
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
                <h2>Edit Project</h2>
            </div>

            <div class="innerContent">
            <div class="row">
                    <div class="col-75">
                        <div class="container">
                        <?php
                            if (isset($_POST['editProject'])) {
                        ?>
                            <form name="editProjectGroup" action="Manager_editProjectGroup.php" method="POST">
                            
                                <input type="hidden" name="projectname" value="<?php echo $projectName; ?>">
                                <input type="hidden" name="startdate" value="<?php echo $startDate; ?>">
                                <input type="hidden" name="enddate" value="<?php echo $endDate; ?>">
                                <input type="hidden" name="mainprojectid" value="<?php echo $mainProjectID; ?>">
                    
                                <div class="row">
                                    <div class="col-50">
                                        <label for="spGroup" style="font-weight:bold;">Specialisation Group</label>
                                        <div class="checkbox-container">
                                            <?php
                                            foreach ($spGroupsProject as $spGroupsProjects):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='spgroup[]' value='". $spGroupsProjects['MainGroupID']."' checked>". $spGroupsProjects['GroupName']."</div>";
                                            endforeach;
                                            
                                            foreach ($spGroups as $spGroup):
                                                echo "<div class='checkbox-team'><input type='checkbox' name='spgroup[]' value='". $spGroup['MainGroupID']."'>". $spGroup['GroupName']."</div>";
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="editProjectGroup" type="submit" class="btn">Save</button>
                                
                            </form>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>