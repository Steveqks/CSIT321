<?php
	session_start();

	include '../Session/session_check_superadmin.php';

	$PlanID = $_SESSION['PlanID'];

	$_SESSION['message1'] = '';
	$_SESSION['message2'] = '';
	$_SESSION['message3'] = '';

	if(isset($_POST['newCS'])){
		$newPrice = $_POST['newPrice'];
		$newUA = $_POST['newUA'];
		$newCS = $_POST['newCS'];
		
		if ($_POST['oldPrice'] != $newPrice){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE plans SET Price = '$newPrice' WHERE plans.planID = '$PlanID'") or die("update Error");
			$_SESSION['message1'] = "<p>Subscription Plan Price updated</p>";							
		}else $_SESSION['message1'] = "";
		
		
		if($_POST['oldUA'] != $newUA){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE plans SET UserAccess = '$newUA' WHERE plans.planID = '$PlanID'") or die("update Error");
			$_SESSION['message2'] = "<p >Subscription Plan User Access updated.</p>";
		}else $_SESSION['message2'] = "";
		
		
		if($_POST['oldCS'] != $newCS){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE plans SET CustomerSupport = '$newCS' WHERE plans.planID = '$PlanID'") or die("update Error");
			$_SESSION['message3'] = "<p >Subscription Plan CustomerSupport updated.</p>";
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
		
			<h2>Edit Plan</h2>

			<?php   
			
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$qres = mysqli_query($db,	"SELECT * FROM plans WHERE plans.planID = '$PlanID'") or die("Select Error");
		
				while ($Row = $qres->fetch_assoc()) {
					$PlanName = $Row['PlanName'];
					$Price =  $Row['Price'];
					$UserAccess = $Row['UserAccess'];
					$CustomerSupport = $Row['CustomerSupport'];
				};
		
				$form = "<form action'' id='ModifyPlan' method='POST'>
						<br>
						<table>
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br><br>
						Plan Name: <input type='text' value='" . $PlanName . "' readonly><br>
						Price: <input type='text' name='oldPrice' value='" . $Price . "' readonly> <br>
						User Access : <input type='text' name='oldUA' value='" . $UserAccess . "' readonly> <br>
						Customer Support : <input type='text' name='oldCS' value='" . $CustomerSupport . "' readonly> <br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						TO
						<br><br>
						Plan Name: <input type='text' value='" . $PlanName . "' readonly ><br>
						Price: <input type='text' name='newPrice' value='" . $Price . "' > <br>
						User Access : <input type='text' name='newUA' value='" . $UserAccess . "' > <br>
						Customer Support : <input type='text' name='newCS' value='" . $CustomerSupport . "' > <br>
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
						document.getElementById('ModifyPlan').submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


