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
			  <a href="Logout.php">Logout</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <form action = "", method = "post">
				<h2>Create Company</h2>
				<input id = "companyName" name = "companyName" type = "text" placeholder = "Company Name" required>
				<input id = "planType" name = "planType" type = "text" placeholder = "Plan Type" required>
				<button id = "submitBtn" name = "submit">Create</button>
			</form>
			
			<?php   
				//create company
				if(isset($_POST['submit'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

					$companyName = $_POST['companyName'];
					$planType = $_POST['planType'];
					
					//check if company exists
					if(isCompanyExists($companyName, $db)){
						echo "<p style='color: red;'>Company \"".$companyName."\" already exists in database</p>";
					}
					//doesn't exist, add to db
					else
					{
						$result = mysqli_query($db,"INSERT INTO company (CompanyID, CompanyName, PlanID, Status) VALUES (NULL, '$companyName', '$planType', '1')") or die("Select Error");
						echo "<p style='color: green;'>Company \"".$companyName."\" added to database.</p>";
					}
					
				}
					
					
				function isCompanyExists(string $cname, mysqli $db):bool{
					$sql = "SELECT * FROM company WHERE CompanyName = '$cname'";
					$qres = mysqli_query($db, $sql); 

					$num_rows=mysqli_num_rows($qres);

					// dont exists
					if($num_rows > 0){
						return true; 
					}
					// exists
					else{
						return false; 
					}
				}
			?>
        </div>
    </div>

</body>
</html>


