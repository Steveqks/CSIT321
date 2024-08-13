<?php
    session_start();
        
    include 'db_connection.php';
    include '../Session/session_check_user_Manager.php';

    $userID = $_SESSION['UserID'];
    $firstName = $_SESSION['FirstName'];
    $companyID = $_SESSION['CompanyID'];

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
    $managerDetails = $result->fetch_assoc();
    

    if (isset($_POST['editAccount'])) {
            
        $fName = $_POST['firstname'];
        $lName = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['pwd'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($password != $confirm_password) {
            header("Location: Manager_editAccount.php?error=Passwords do not match. Please try again.");
            exit();
        }

        $stmt = $conn->prepare("UPDATE existinguser SET FirstName = ?, LastName = ?, Email = ?, Password = ? WHERE UserID = ?");

        $stmt->bind_param("ssssi",$fName,$lName,$email,$password,$userID);

        if ($stmt->execute()) {

            // Close the statement and connection
            $stmt->close();
            CloseCon($conn);

            header("Location: Manager_editAccount.php?message=Account details updated successfully.");
            exit();

        } else {

            // Close the statement and connection
            $stmt->close();
            CloseCon($conn);

            header("Location: Manager_editAccount.php?error=Error updating account details.");
            exit();
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
    
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var errorMessage = document.getElementById("error-message");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match. Please try again.";
                return false;
            } else {
                errorMessage.textContent = "";
                return true;
            }
        }
    </script>
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
            <h2>Edit User Account</h2>

            <div class="innerContent">
                <div class="row">
                    <div class="col-75">
                        <div class="container">
                            <form name="editAccount" action="Manager_editAccount.php" method="POST" onsubmit="return validateForm()">
                                
                                <div class="row">
                                    <div class="col-50">
                                        <label for="firstname">First Name</label>
                                        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($managerDetails['FirstName']); ?>" required>
                                            
                                        <label for="lastname">Last Name</label>
                                        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($managerDetails['LastName']); ?>" required>

                                        <label for="gender">Gender</label>
                                        <select id="gender" name="gender" disabled>
                                            <?php echo "<option value='". $managerDetails['Gender']."'>" . $managerDetails['Gender']."</option>"; ?>
                                        </select>
                                            
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($managerDetails['Email']); ?>" required>
                                            
                                        <label for="role">Role</label>
                                        <select id="role" name="role" disabled>
                                            <?php echo "<option value='". $managerDetails['Role']."'>" . $managerDetails['Role']."</option>"; ?>
                                        </select>
                                    
                                    </div>
                                    <div class="col-50">

                                        <label for="specialisation">Specialisation</label>
                                        <select id="specialisationidname" name="specialisationidname" disabled>

                                            <?php echo "<option value='". $managerDetails['SpecialisationID']." ".$managerDetails['SpecialisationName']."'>" . $managerDetails['SpecialisationName']."</option>"; ?>

                                        </select>
                                            
                                        <label for="pwd">Password</label>
                                        <input type="password" id="password" name="pwd" value="<?php echo htmlspecialchars($managerDetails['Password']); ?>" required>

                                        <label for="confirm_password">Confirm Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <?php
                                        if (isset($_GET['message'])) {
                                            echo '<div class="success-message">' . htmlspecialchars($_GET['message']) . '</div>';
                                        } elseif (isset($_GET['error'])) {
                                            echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                                        }
                                    ?>

                                    <button name="editAccount" type="submit" class="btn">Save</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>