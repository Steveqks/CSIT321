<?php
session_start();

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
		
			<h2>Edit Company</h2>

			<?php   
				$form = "<form action'' method='POST'>
						<br>
						<table>
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br>
						company id: <input type='text' value=" . $_SESSION['companyID'] . " readonly><br>
						company name: <input type='text' name='oldCompanyName' value=" . $_SESSION['companyName'] . " readonly> <br>
						subscription plan: <input type='text' name='oldPlanID' value=" . $_SESSION['planID'] . " readonly> <br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						TO
						<br>
						company id: <input type='text' name='companyID' value=" . $_SESSION['companyID'] . " readonly> <br>
						company name: <input type='text' name='companyName' value=" . $_SESSION['companyName'] . "><br>
						subscription plan: <input type='text' name='planID' value=" . $_SESSION['planID'] . "><br>
						<input type='submit' name='submitChange' value='Update'>
						</form>
							</td>
						</tr>
						</table>
							";
				echo $form;
				
				if (@$_SESSION['message1']){
				echo @$_SESSION['message1'];
				}
				if (@$_SESSION['message2']){
				echo @$_SESSION['message2'];
				}
				
				if(isset($_POST['submitChange'])){
					$companyName = $_POST['companyName'];			
					$companyID = $_POST['companyID'];
					$planID = $_POST['planID'];
					
					if ($_POST['oldCompanyName'] != $_POST['companyName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

						//check if companyName exists.
						$result = mysqli_query($db,	"SELECT CompanyName FROM company WHERE company.CompanyName = '$companyName'") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result);
						// dont exists
						if($num_rows == 0){
							$result2 = mysqli_query($db,"UPDATE company SET CompanyName = '$companyName' WHERE company.CompanyID = '$companyID'") or die("update Error");
							$_SESSION['message1'] = "<p style='color: green;'>Company new name ->\"". $companyName. "\"</p>";							
							$_SESSION['companyName'] = $companyName;
						}
						else{
							$_SESSION['message1'] = "Company name already exists";
							$_POST['companyName'] = $_POST['oldCompanyName'];
						}
					}else $_SESSION['message1'] = "";
					
					
					if($_POST['oldPlanID'] != $planID){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

						//check if planID exists already.
						$result3 = mysqli_query($db, "SELECT * FROM plans WHERE planID = '$planID'") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result3);
						// dont exists
						if($num_rows > 0){
							$result2 = mysqli_query($db,"UPDATE company SET PlanID = '$planID' WHERE company.CompanyID = '$companyID'") or die("update Error");
							$_SESSION['message2'] = "<p style='color: green;'>Company subscription plan updated.</p>";
							$_SESSION['planID'] = $planID;
						}
						else{
							$_SESSION['message2'] = "Subscription plan does not exists";
						}
					}else $_SESSION['message2'] = "";
					
					header('Location: superadmin_manageCompany_view-edit.php');
					exit;
				}
				
			?>
        </div>
    </div>

</body>
</html>


