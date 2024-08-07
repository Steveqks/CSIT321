<?php
session_start();

	include '../Session/session_check_superadmin.php';

	include 'db_connection.php';


	$_SESSION['message1'] = '';
	$_SESSION['message2'] = '';
	$_SESSION['message3'] = '';

	if(isset($_POST['newCompanyUEN'])){
		$companyName = $_POST['companyName'];			
		$companyID = $_POST['companyID'];
		$newCompanyUEN = $_POST['newCompanyUEN'];
		$planID = $_POST['planID'];
		
		if ($_POST['oldCompanyName'] != $_POST['companyName']){
			//check if companyName exists.
			$result = mysqli_query($db,	"SELECT CompanyName FROM company WHERE company.CompanyName = '$companyName'") or die("Select Error");

			$num_rows=mysqli_num_rows($result);
			// dont exists
			if($num_rows == 0){
				$result2 = mysqli_query($db,"UPDATE company SET CompanyName = '$companyName' WHERE company.CompanyID = '$companyID'") or die("update Error");
				$_SESSION['message1'] = "<p >Company Name changed</p>";							
				$_SESSION['companyName'] = $companyName;
			}
			else{
				$_SESSION['message1'] = "Company name already exists";
				$_POST['companyName'] = $_POST['oldCompanyName'];
			}
		}else $_SESSION['message1'] = "";
		
		
		if($_POST['oldPlanID'] != $planID){
			//check if planID exists already.
			$result3 = mysqli_query($db, "SELECT * FROM plans WHERE planID = '$planID'") or die("Select Error");

			$num_rows=mysqli_num_rows($result3);
			// dont exists
			if($num_rows > 0){
				$result2 = mysqli_query($db,"UPDATE company SET PlanID = '$planID' WHERE company.CompanyID = '$companyID'") or die("update Error");
				$_SESSION['message2'] = "<p >Company subscription plan updated.</p>";
				$_SESSION['planID'] = $planID;
			}
			else{
				$_SESSION['message2'] = "Subscription plan does not exists";
			}
		}else $_SESSION['message2'] = "";
		
		
		if($_POST['oldCompanyUEN'] != $newCompanyUEN){
			//check if planID exists already.
			$result3 = mysqli_query($db, "SELECT * FROM company WHERE CompanyUEN = '$newCompanyUEN' ") or die("Select Error");

			$num_rows=mysqli_num_rows($result3);
			// dont exists
			if($num_rows > 0){
				$_SESSION['message3'] = "Company UEN already exists";
			}
			else{
				$result2 = mysqli_query($db,"UPDATE company SET CompanyUEN = '$newCompanyUEN' WHERE company.CompanyID = '$companyID'") or die("update Error");
				$_SESSION['message3'] = "<p >Company UEN updated.</p>";
				$_SESSION['companyUEN'] = $newCompanyUEN;
				
			}
		}else $_SESSION['message3'] = "";
		

	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">

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
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Edit Company</h2>

			<?php   
				$form = "<form action'' id='ModifyAccount' method='POST' style='
																				flex: 0 0 48%;
																				display: inline-flex;
																				justify-content: space-between;
																				padding: 8px;
																				border: 1px solid #ddd;
																				border-radius: 4px;
																				box-sizing: border-box;
																				width: 80%;
																				margin-bottom: 15px;
																				margin-bottom: 5px;
																				display: flex;
																				flex-direction: column;
																				margin-bottom: 15px;
																				background-color: #f0f0f0;
																				padding: 20px;
																				border-radius: 5px;
																				max-width: 600px;
																				display: flex;
																				flex-direction: column;
																					'>
						
						<table >
						<input type='hidden' name='oldCompanyName' value='" . $_SESSION['companyName'] . "' readonly> 
						<input type='hidden' name='oldCompanyUEN' value=" . $_SESSION['companyUEN'] . " readonly> 
						<input type='hidden' name='oldPlanID' value=" . $_SESSION['planID'] . " readonly>
						<input type='hidden' name='companyID' value=" . $_SESSION['companyID'] . " readonly>
						<br><br><br><br>
						<tr>
							<td>
								Company Name: <input type='text' name='companyName' value='" . $_SESSION['companyName'] . "' maxlength='16'> <br>
								Company UEN: <input type='text' name='newCompanyUEN' value=" . $_SESSION['companyUEN'] . "  maxlength='10'> <br>
							</td>
							
							<td>
								Subscription Plan: <input type='text' name='planID' value=" . $_SESSION['planID'] . " maxlength='1'><br><br>
								<input type='button' value='Update' onclick='confirmDiag();'>
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
				if (@$_SESSION['message3']){
				echo @$_SESSION['message3'];
				}
				
				
				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Submit Changes?");
					if (result)
					{
						document.getElementById('ModifyAccount').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


