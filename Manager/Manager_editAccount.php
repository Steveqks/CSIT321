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
        $userStatus = 1;

        // get manager's details to edit
        $sql = "SELECT a.*, b.SpecialisationName FROM existinguser a
                LEFT JOIN specialisation b ON a.SpecialisationID = b.SpecialisationID
                WHERE a.UserID = ".$userID;
    
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $managerDetails = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_POST['editAccount'])) {
            
            $email = $_POST['email'];
            $password = $_POST['pwd'];

            $sql = "SELECT UserID FROM existinguser
                    WHERE email LIKE '%".$email."%'";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                echo "<script type='text/javascript'>";
                echo "alert('User exist.');";
                echo "window.location = 'Manager_editAccount.php';";
                echo "</script>";

            } else {

                $stmt = $conn->prepare("UPDATE existinguser SET Email=?,Password=? WHERE UserID=?");

                $stmt->bind_param("ssi",$email,$password,$userID);

                if ($stmt->execute()) {
                    echo "<script type='text/javascript'>";
                    echo "alert('Account has been updated successfully.');";
                    echo "window.location = 'Manager_editAccount.php';";
                    echo "</script>";
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
        <div class="navBar">
            <nav>
                <ul>
                    <li><a href="Manager_viewTasks.php"><?php echo "$firstName, Staff(Manager)"?></a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&manageaccount=true">Manage Account</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&taskmanagenent=true">Task Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&leavemanagenent=true">Leave Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&attendancemanagenent=true">Time/Attendance Tracking</a></li>
                    <li><a href="Manager_viewNewsFeed.php">News Feed Management</a></li>
                    <li><a href="Manager_allHeadings.php?employeetype=Manager&projectmanagenent=true">Project Management</a></li>
                    <li><a href="Logout.php">Logout</a></li>
                    
                </ul>
            </nav>
        </div>

            
            
        <!-- Right Section (Activity) -->
        <div class="content">
            <div class="task-header">
                <i class="fas fa-user"></i>
                    <h2>Create User Account</h2>
            </div>

            <div class="innerContent">
            <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editAccount" action="Manager_editAccount.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="firstname">First Name</label>
                                        <input type="text" id="firstname" name="firstname" value="<?php foreach ($managerDetails as $managerDetail): echo $managerDetail['FirstName']; endforeach; ?>" disabled>
                                        
                                        <label for="lastname">Last Name</label>
                                        <input type="text" id="lastname" name="lastname" value="<?php foreach ($managerDetails as $managerDetail): echo $managerDetail['LastName']; endforeach; ?>" disabled>

                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" disabled>
                                            <?php foreach ($managerDetails as $managerDetail):
                                                echo "<option value='". $managerDetail['Gender']."'>" . $managerDetail['Gender']."</option>";
                                            endforeach; ?>
                                        </select>
                                        
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?php foreach ($managerDetails as $managerDetail): echo $managerDetail['Email']; endforeach; ?>">
                                        
                                        <label for="role">Role</label>
                                        <select id="role" name="role" disabled>
                                            <?php foreach ($managerDetails as $managerDetail):
                                                echo "<option value='". $managerDetail['Role']."'>" . $managerDetail['Role']."</option>";
                                            endforeach; ?>
                                        </select>
                                
                                    </div>
                                    <div class="col-50">

                                        <label for="specialisation">Specialisation</label>
                                        <select id="specialisationidname" name="specialisationidname" disabled>

                                            <?php foreach ($managerDetails as $managerDetail):
                                                echo "<option value='". $managerDetail['SpecialisationID']." ".$managerDetail['SpecialisationName']."'>" . $managerDetail['SpecialisationName']."</option>";
                                            endforeach; ?>

                                        </select>
                                        
                                        <label for="pwd">Password</label>
                                        <input type="text" id="pwd" name="pwd" value="<?php foreach ($managerDetails as $managerDetail): echo $managerDetail['Password']; endforeach; ?>">

                                    </div>

                                <button name="editAccount" type="submit" class="btn">Save</button>
                                
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>