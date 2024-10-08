<?php
session_start();

include '../Session/session_check_companyadmin.php';
	
include 'db_connection.php';
	
	$_SESSION['message1'] = "";
	$_SESSION['message2'] = "";
	$_SESSION['message3'] = "";
	$_SESSION['message4'] = "";
	
	$_SESSION['message1'] ='';
	$_SESSION['message2'] ='';
	$_SESSION['message3'] ='';
	$_SESSION['message4'] ='';
	

	
	if(isset($_POST['Password'])){
		$FirstName = $_POST['FirstName'];			
		$LastName = $_POST['LastName'];			
		$newEmail = $_POST['newEmail'];			
		$oldEmail = $_POST['oldEmail'];
		$Password = $_POST['Password'];			
		
		//email has changes
		if($newEmail != $oldEmail)
		{
			$sql = "SELECT * FROM companyadmin WHERE Email = '$newEmail'";
			$qres = mysqli_query($db, $sql); 
			$num_rows=mysqli_num_rows($qres);

			// email exists
			if($num_rows > 0){
				$_SESSION['message1'] = "<p>Email Address is already in use.</p>";
			}
			// dont exists
			else{
				$sql = "SELECT * FROM superadmin WHERE Email = '$newEmail'";
				$qres = mysqli_query($db, $sql); 
				$num_rows=mysqli_num_rows($qres);
				
				// exists
				if($num_rows > 0){
					//return error
					$_SESSION['message1'] = "<p>Email Address is already in use.</p>";
				}
				// dont exists
				else
				{
					$sql = "SELECT * FROM existinguser WHERE Email = '$newEmail'";
					$qres = mysqli_query($db, $sql); 
					$num_rows=mysqli_num_rows($qres);
				
					if($num_rows > 0){
					//return error
					$_SESSION['message3'] = "<p>Email Address is already in use.</p>";
					}
					// dont exists
					else{
						$result2 = mysqli_query($db,"UPDATE companyadmin SET Email = '$newEmail' WHERE CAdminID = '$cadminID'") or die("update Error");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET FirstName = '$FirstName' WHERE CAdminID = '$cadminID' ") or die("update Error");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET LastName = '$LastName' WHERE CAdminID = '$cadminID'") or die("update Error");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET Password = '$Password' WHERE CAdminID = '$cadminID'") or die("update Error");
						$_SESSION['message3'] = "<p>Account settings has been changed.</p>";
					}
				}
			}
		}
		else
		{
						$result2 = mysqli_query($db,"UPDATE companyadmin SET FirstName = '$FirstName' WHERE CAdminID = '$cadminID' ") or die("update Error");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET LastName = '$LastName' WHERE CAdminID = '$cadminID'") or die("update Error");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET Password = '$Password' WHERE CAdminID = '$cadminID'") or die("update Error");
						$_SESSION['message3'] = "<p>Account settings has been changed.</p>";

		}
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
<?php include_once('navigation.php') ?>

	<!-- Right Section (Activity) -->
	<div style="width: 80%; padding: 10px;">

	<h2>Manage Account</h2>
	<?php   
		$cadminID = $_SESSION['cadminID'];
		
				//get Super Admin data
				$result = mysqli_query($db,	"SELECT * FROM companyadmin WHERE CAdminID = '$cadminID'") or die("Select Error");
			
				while($Row = $result->fetch_assoc()){
				$form = "<form  action='' id='ModifyAccount'  method='POST' style='
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
																			max-width: 500px;
																			display: flex;
																			flex-direction: column;
																				'>
						<br>
							<table >
								<tr>
									<td>
										First Name: <input type='text' name='FirstName' value='" . $Row['FirstName'] . "' ><br>
										Last Name: <input type='text' name='LastName' value='" . $Row['LastName'] . "' > <br>
										<input type='hidden' name='oldEmail' value=" . $Row['Email'] . " readonly> 
									</td>
										<td>
										Email Address: <input type='text' name='newEmail' value=" . $Row['Email'] . " > <br>
										Password: <input type='password' name='Password' value=" . $Row['Password'] . " > <br>
										</td>
								</tr>
							</table>
							<input type='button' value='Save Changes' onclick='confirmDiag();' style='horizontal-align: right; width: 25%;'>
						</form>
							";
				}
				echo $form;
				
				echo @$_SESSION['message1'];
				echo @$_SESSION['message2'];
				echo @$_SESSION['message3'];
				echo @$_SESSION['message4'];
		
		
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


