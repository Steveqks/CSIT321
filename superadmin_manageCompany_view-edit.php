<?php
session_start();

if (isset($_POST['submitChange'])) {
	if ($_SESSION['code'] == "changenameNplan"){
		$_SESSION['companyID'] = $_POST['companyID'];
		$_SESSION['companyName'] = $_POST['companyName'];
		$_SESSION['planID'] = $_POST['planID'];
		header('Location: superadmin_manageCompany_view-edit.php');
	}
	if ($_SESSION['code'] == "changeplan"){
		$_SESSION['companyID'] = $_POST['companyID'];
		$_SESSION['companyName'] = $_POST['companyName'];
		$_SESSION['planID'] = $_POST['planID'];
		header('Location: superadmin_manageCompany_view-edit.php');
	}
	else if ($_SESSION['code'] == "nochange"){
		header('Location: superadmin_manageCompany_view-edit.php');
	}
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
			  <a href="#">Home</a>
			  <a href="#">Link 1</a>
			  <a href="#">Link 2</a>
			  <a href="#">Link 3</a>
			  <a href="#">Link 4</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <!-- Add more content as needed -->
			<?php   
				echo "Edit Company:<br>";
				$form = "<form action'' method='POST'>
						<br>
						FROM 
						<input type='text' value=" . $_SESSION['companyID'] . " readonly>
						<input type='text' name='oldCompanyName' value=" . $_SESSION['companyName'] . " readonly>
						<input type='text' name='oldPlanID' value=" . $_SESSION['planID'] . " readonly>
						<br>
						TO
						<input type='text' name='companyID' value=" . $_SESSION['companyID'] . " readonly>
						<input type='text' name='companyName' value=" . $_SESSION['companyName'] . ">
						<input type='text' name='planID' value=" . $_SESSION['planID'] . ">
						<input type='submit' name='submitChange' value='Update'>
						</form>";
				echo $form;
				
				if ($_SESSION['message']){
				echo $_SESSION['message'];
				}
				
				if(isset($_POST['submitChange'])){
					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
					$companyName = $_POST['companyName'];			
					$companyID = $_POST['companyID'];
					$planID = $_POST['planID'];
					
					//check if companyName exists.
					$result = mysqli_query($db,	"SELECT CompanyName FROM company WHERE company.CompanyName = '$companyName'") or die("Select Error");
		
					$num_rows=mysqli_num_rows($result);
					// dont exists
					if($num_rows == 0){
						//check if planID exists already.
						$result2 = mysqli_query($db,	"SELECT * FROM plans WHERE planID = '$planID'") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result2);
						// dont exists
						if($num_rows > 0){
							$result2 = mysqli_query($db,"UPDATE company SET CompanyName = '$companyName', PlanID = '$planID' WHERE company.CompanyID = '$companyID'") or die("update Error");
							$_SESSION['message'] = "<p style='color: green;'>Company \"". $companyName. "\"updated.</p>". "uhhh name n plan changed";
							$_SESSION['code'] = "changenameNplan";
						}
						if($num_rows == 0){
							$_SESSION['code'] = "nochange";
							$_POST['planID'] = $_POST['oldPlanID'];
							$_SESSION['message'] = "plan does not exists";
						}
					}
					// companyname exists
					else if (($_POST['oldCompanyName'] == $companyName) && ($planID != $_POST['oldPlanID'] )){
						//check if planID exists already.
						$result2 = mysqli_query($db,	"SELECT * FROM plans WHERE planID = '$planID'") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result2);
						// dont exists
						
						if($num_rows == 0){
							$_SESSION['code'] = "nochange";
							$_POST['planID'] = $_POST['oldPlanID'];
							$_SESSION['message'] = "plan does not exists";
						}
						if($num_rows > 0){
							$result2 = mysqli_query($db,"UPDATE company SET CompanyName = '$companyName', PlanID = '$planID' WHERE company.CompanyID = '$companyID'") or die("update Error");
							$_SESSION['message'] = "<p style='color: green;'>Company \"". $companyName. "\"updated.</p>". "plan only changed";
							$_SESSION['code'] = "changeplan";
						}
					}
				}
				
			?>
        </div>
    </div>

</body>
</html>


