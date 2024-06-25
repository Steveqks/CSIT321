<?php
session_start();
				
	$companyID = $_SESSION['companyID'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="a.css">

    <title>TrackMySchedule</title>
</head>
<body>

    <!-- Top Section -->
	<div style="border: 1px solid black; height: 20vh; overflow: hidden; text-align: left;">
        <img src="tms.png" alt="TrackMySchedule Logo" style="height: 100%; width: auto;">
    </div>

    <!-- Middle Section -->
    <div style="display: flex; border: 1px solid black; height: 80vh;">
        
        <!-- Left Section (Navigation) -->
			<div class="vertical-menu" style="border-right: 1px solid black; padding: 0px;">
				<a href="companyadmin_homepage.php">Home</a>
				<a href="companyadmin_ManageAccount.php">Manage Account</a>
				<a href="companyadmin_ManageUserAccounts_create.php">Manage User Accounts > Create</a>
				<a href="companyadmin_ManageUserAccounts_view.php">Manage User Accounts > View</a>
				<a href="companyadmin_specialisation_create.php">Manage Specialisation > Create </a>
				<a href="companyadmin_specialisation_view_delete.php">Manage Specialisation > View</a>
				<a href="companyadmin_teamManagement_create.php">Manage Team > Create </a>
				<a href="companyadmin_teamManagement_view_delete.php">Manage Team > View</a>
				<a href="companyadmin_ManageCalendar_view.php">Manage Calendar > Create Entry</a>
			<a href="Logout.php">Logout</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "", method = "post">
				<h2>Create Calendar Entry</h2>

					<h4>First Name: <input name = "fname" type = "text" placeholder = "first name" required>
					</h4>
					<h4>Last Name: <input name = "lname" type = "text" placeholder = "last name" required>
					</h4>
					<h4>Email address: <input name = "emailadd" type = "text" placeholder = "email address" required>
					</h4>
					<h4>Gender: <input name = "gender" type = "text" placeholder = "gender" required>
					</h4>
					<h4>Password: <input name = "password" type = "password" placeholder = "password" required>
					</h4>
					<h4>  <label for="Role">Role:</label>
					  <select name="role" id="">
						  <option value="Manager">Manager</option>
						  <option value="FT">FT</option>
						  <option value="PT">PT</option>
					  </select>
					</h4>
					
							<?php
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$sql = "SELECT * FROM specialisation WHERE CompanyID = '$companyID' ";
						$qres = mysqli_query($db, $sql); 
						
						$select = 	"<label for='Specialisation'><h4>Specialisation:</label>
									<select name='specialisationID' id=''>";		
						while ($Row = $qres->fetch_assoc()) 
						{
							$select .= "<option value ='" . $Row['SpecialisationID'] . "'> ID:" . $Row['SpecialisationID']. ", " 
									. $Row['SpecialisationName'] . " </option>";
						}
						$select .= "</select> </h4>";
						echo $select;
						
					?>
					<h4>Status: <input name = "status" type = "text" placeholder = "status" required>
					</h4>
					<button id = "submitBtn" name = "submit">Create</button>
			</form>
			
			<?php   
			
			
				//create user
				if(isset($_POST['submit'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");


					$fname = $_POST['fname'];
					$lname = $_POST['lname'];
					$emailadd = $_POST['emailadd'];
					$gender = $_POST['gender'];
					$password = $_POST['password'];
					
					$role = $_POST['role'];
					$specialisation = $_POST['specialisationID'];
					$status = $_POST['status'];
					
					//check if email exists in existinguser
					if(isUserEmailExists($emailadd, $db)){
						echo "<p style='color: red;'>Email Address\"".$emailadd."\" already in use.</p>";
					}
					//doesn't exist, add to db
					else
					{
						$result = mysqli_query($db,"INSERT INTO existinguser 
													(UserID, CompanyID, SpecialisationID, Role, FirstName, LastName, Gender, Email, Password, Status) 
											VALUES 	(NULL, '$companyID', '$specialisation', '$role', '$fname', '$lname', '$gender', '$emailadd', '$password', '$status')") or die("Select Error");
						echo "<p style='color: green;'>User Account for \"".$fname." ".$lname."\" created.</p>";
					}
				}
					
				function isUserEmailExists(string $emailadd, mysqli $db):bool{
					$sql = "SELECT * FROM existinguser WHERE Email = '$emailadd'";
					$qres = mysqli_query($db, $sql); 

					$num_rows=mysqli_num_rows($qres);

					// exists
					if($num_rows > 0){
						return true; 
					}
					// dont exists
					else{
						return false; 
					}
				}
				
			?>
        </div>
    </div>

</body>
</html>


