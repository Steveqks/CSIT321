<?php
	session_start();

	include '../Session/session_check_superadmin.php';

	include 'db_connection.php';

	$PlanID = $_SESSION['PlanID'];

	$_SESSION['message1'] = '';
	$_SESSION['message2'] = '';
	$_SESSION['message3'] = '';

	if(isset($_POST['newCS'])){
		$newPrice = $_POST['newPrice'];
		$newUA = $_POST['newUA'];
		$newCS = $_POST['newCS'];
		
		if ($_POST['oldPrice'] != $newPrice){
			$result2 = mysqli_query($db,"UPDATE plans SET Price = '$newPrice' WHERE plans.planID = '$PlanID'") or die("update Error");
			$_SESSION['message1'] = "<p>Subscription Plan Price updated</p>";							
		}else $_SESSION['message1'] = "";
		
		
		if($_POST['oldUA'] != $newUA){
			$result2 = mysqli_query($db,"UPDATE plans SET UserAccess = '$newUA' WHERE plans.planID = '$PlanID'") or die("update Error");
			$_SESSION['message2'] = "<p >Subscription Plan User Access updated.</p>";
		}else $_SESSION['message2'] = "";
		
		
		if($_POST['oldCS'] != $newCS){
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
			
				$qres = mysqli_query($db,	"SELECT * FROM plans WHERE plans.planID = '$PlanID'") or die("Select Error");
		
				while ($Row = $qres->fetch_assoc()) {
					$PlanName = $Row['PlanName'];
					$Price =  $Row['Price'];
					$UserAccess = $Row['UserAccess'];
					$CustomerSupport = $Row['CustomerSupport'];
				};
		
				$form = "<form action'' id='ModifyPlan' method='POST' style='
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
						<br>
						<table>
						<tr>

						<input type='hidden' value='" . $PlanName . "' readonly><br>
						<input type='hidden' name='oldPrice' value='" . $Price . "' readonly> <br>
						<input type='hidden' name='oldUA' value='" . $UserAccess . "' readonly> <br>
						<input type='hidden' name='oldCS' value='" . $CustomerSupport . "' readonly> <br>
						<br>
							<td >
				
						Plan Name: <input type='text' value='" . $PlanName . "' readonly ><br>
						Price: <input type='text' name='newPrice' value='" . $Price . "' > <br>
						
						<input type='button' value='Update' onclick='confirmDiag();'>
							</td><td>
						User Access : <input type='text' name='newUA' value='" . $UserAccess . "' > <br>
						Customer Support : <input type='text' name='newCS' value='" . $CustomerSupport . "' > <br><br>
						
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


