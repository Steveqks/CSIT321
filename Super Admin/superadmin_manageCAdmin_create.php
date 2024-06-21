<?php
session_start();

if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_edit_specialisation.php');
	exit;
}
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
			  <a href="superadmin_homepage.php">Home</a>
			  <a href="superadmin_ManageAccount.php">Manage Account</a>
			  <a href="superadmin_manageCompany_create.php">Manage Company > Create Company </a>
			  <a href="superadmin_manageCompany_view.php">Manage Company > View Company </a>
			  <a href="superadmin_manageCAdmin_approve_unreg_user.php">Approve New Company (Create New Company & Company Admin)</a>
			  <a href="superadmin_manageCAdmin_create.php">Manage Company Admin > Create Company Admin</a>
			  <a href="superadmin_manageCAdmin_view_delete.php">Manage Company Admin > View Company Admin</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "", method = "post">
				<h2>Create Company Admin</h2>
					<h4>Company ID: <input name = "companyID" type = "text" placeholder = "Company ID" required>
					</h4>
					<h4>First Name: <input name = "fname" type = "text" placeholder = "first name" required>
					</h4>
					<h4>Last Name: <input name = "lname" type = "text" placeholder = "last name" required>
					</h4>
					<h4>Email address: <input name = "emailadd" type = "text" placeholder = "email address" required>
					</h4>
					<h4>Password: <input name = "password" type = "text" placeholder = "password" required>
					</h4>
					<button id = "submitBtn" name = "submit">Create</button>
			</form>
			
			<?php   
				//create company
				if(isset($_POST['submit'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

					$companyID = $_POST['companyID'];
					$fname = $_POST['fname'];
					$lname = $_POST['lname'];
					$emailadd = $_POST['emailadd'];
					$password = $_POST['password'];
					
					//check if company exists
					if(isCompanyExists($companyID, $db)){
						//check if email already exists in companyadmin
						if(isCAEmailExists($emailadd, $db)){
							echo "<p style='color: red;'>email already exists.</p>";
						}
						else{
						$result = mysqli_query($db,"INSERT INTO companyadmin (CAdminID, CompanyID, FirstName, LastName, Email, Password) VALUES (NULL, '$companyID', '$fname', '$lname', '$emailadd', '$password')") or die("Select Error");
						echo "<p style='color: green;'>Company admin\"".$fname." ".$lname."\" added to database.</p>";
						}
					}
					//doesn't exist, add to db
					else
					{
						echo "<p style='color: red;'>Company id \"".$companyID."\" does exists in database</p>";
					}
				}
					
				function isCompanyExists(string $companyID, mysqli $db):bool{
					$sql = "SELECT * FROM company WHERE CompanyID = '$companyID'";
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
				function isCAEmailExists(string $emailadd, mysqli $db):bool{
					$sql = "SELECT * FROM companyadmin WHERE Email = '$emailadd'";
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


