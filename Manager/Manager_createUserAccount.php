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
    $userStatus = 1;

    // get specialisation for the select option
    $sql = "SELECT * FROM specialisation WHERE CompanyID = ".$companyID." ORDER BY SpecialisationName ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $specialisations = $result->fetch_all(MYSQLI_ASSOC);


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
            
            // Close the database connection
            $stmt->close();
            CloseCon($conn);

            header("Location: Manager_createUserAccount.php?error=User exist.");
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO existinguser (CompanyID,SpecialisationID,Role,FirstName,LastName,Gender,Email,Password,Status) VALUES (?,?,?,?,?,?,?,?,?)");

            $stmt->bind_param("iissssssi",$companyID,$specialisationID,$role,$firstName,$lastName,$gender,$email,$password,$userStatus);

            if ($stmt->execute()) {
                // Close the database connection
                $stmt->close();
                CloseCon($conn);
            
                header("Location: Manager_createUserAccount.php?message=Account has been created successfully.");
                exit();
            } else {
                // Close the database connection
                $stmt->close();
                CloseCon($conn);
                
                header("Location: Manager_createUserAccount.php?error=Error updating account details.");
                exit();
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
            <h2>Create User Account</h2>

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
                                
                                <?php
                                    if (isset($_GET['message'])) {
                                        echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                    } elseif (isset($_GET['error'])) {
                                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                    }
                                ?>

                                <button name="createAccount" type="submit" class="btn">Save</button>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>