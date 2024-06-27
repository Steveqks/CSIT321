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

        // get specialisation for the select option
        $sql = "SELECT * FROM specialisation WHERE CompanyID = ".$companyID." ORDER BY SpecialisationName ASC";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $specialisations = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        if (isset($_POST['createAccount'])) {

            $firstName = $_POST['firstname'];
            $lastName = $_POST['lastname'];
            $gender = $_POST['gender'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['pwd'];

            $specialisationIDName = $_POST['specialisationidname'];

            $specialisationIDNameE = explode(" ", $specialisationIDName);
        
            $specialisationID = $specialisationIDNameE[0];
                
            $specialisationName = $specialisationIDNameE[1];

            $sql = "SELECT UserID FROM existinguser
                    WHERE email LIKE '%".$email."%'";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                echo "<script type='text/javascript'>";
                echo "alert('User exist.');";
                echo "window.location = 'Manager_createUserAccount.php';";
                echo "</script>";

            } else {
                $stmt = $conn->prepare("INSERT INTO existinguser (CompanyID,SpecialisationID,Role,FirstName,LastName,Gender,Email,Password,Status) VALUES (?,?,?,?,?,?,?,?,?)");

                $stmt->bind_param("iissssssi",$companyID,$specialisationID,$role,$firstName,$lastName,$gender,$email,$password,$userStatus);

                if ($stmt->execute()) {
                    echo "<script type='text/javascript'>";
                    echo "alert('Account has been created successfully.');";
                    echo "window.location = 'Manager_createUserAccount.php';";
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
                            <form name="createAccount" action="Manager_createUserAccount.php" method="POST">
                            
                                <div class="row">
                                    <div class="col-50">
                                        <label for="firstname">First Name</label>
                                        <input type="text" id="firstname" name="firstname" required>
                                        
                                        <label for="lastname">Last Name</label>
                                        <input type="text" id="lastname" name="lastname" required>

                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" required>
                                            <option value="F">Female</option>
                                            <option value="M">Male</option>
                                        </select>
                                        
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" required>
                                        
                                        <label for="role">Role</label>
                                        <select id="role" name="role" required>
                                            <option value="FT">Full-Time</option>
                                            <option value="PT">Part-Time</option>
                                        </select>
                                
                                    </div>
                                    <div class="col-50">

                                        <label for="specialisation">Specialisation</label>
                                        <select id="specialisationidname" name="specialisationidname" required>
                                            <?php foreach ($specialisations as $specialisation):
                                                echo "<option value='". $specialisation['SpecialisationID']." ".$specialisation['SpecialisationName']."'>" . $specialisation['SpecialisationName']."</option>";
                                            endforeach; ?>
                                        </select>
                                        
                                        <label for="pwd">Password</label>
                                        <input type="text" id="pwd" name="pwd" required>

                                    </div>

                                    

                                <button name="createAccount" type="submit" class="btn">Save</button>
                                
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>