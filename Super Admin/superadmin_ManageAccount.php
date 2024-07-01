<?php
session_start();


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
		
			<h2>Manage Account</h2>
			<?php   
				$SAdminID = $_SESSION['SAdminID'];
				$confirmMSG = 'want to confirm?';	
				
				//get Super Admin data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"SELECT * FROM superadmin WHERE SAdminID = '$SAdminID'") or die("Select Error");
			
				while($Row = $result->fetch_assoc()){
				$form = "<form  action='' id='ModifyAccount'  method='POST'>
						<br>
							<table >
								<tr>
									<td style='border: 2px solid black; border-collapse: collapse;'>
										FROM 
										<br><br>
										
										First Name: <input type='text' name='oldFirstName' value='" . $Row['FirstName'] . "' readonly><br>
										Last Name: <input type='text' name='oldLastName' value='" . $Row['LastName'] . "' readonly> <br>
										Email Address: <input type='text' name='oldEmail' value=" . $Row['Email'] . " readonly> <br>
									<br></td>
											
									<td style='border: 2px solid black; border-collapse: collapse;'> 
										TO
										<br><br>
										First Name: <input type='text' name='newFirstName' value='" . $Row['FirstName'] . "' > <br>
										Last Name: <input type='text' name='newLastName' value='" . $Row['LastName'] . "'><br>
										Email Address: <input type='text' name='newEmail' value=" . $Row['Email'] . "><br>
										
										<input type='button' value='Update' onclick='confirmDiag();'>
									</td>
								</tr>
							</table>
						</form>
							";
				}
				echo $form;
				
				echo @$_SESSION['message1'];
				echo @$_SESSION['message2'];
				echo @$_SESSION['message3'];
				
				if(isset($_POST['newEmail'])){
					$newFirstName = $_POST['newFirstName'];			
					$newLastName = $_POST['newLastName'];			
					$newEmail = $_POST['newEmail'];			
					
					//check if there are changes in first name
					if ($_POST['oldFirstName'] != $_POST['newFirstName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
							$result2 = mysqli_query($db,"UPDATE superadmin SET FirstName = '$newFirstName' WHERE SAdminID = '$SAdminID' ") or die("update Error");
							$_SESSION['message1'] = "<p>First name has been changed.</p>";
						
					}
					else $_SESSION['message1'] = "";
					
					//check if there are changes in last name
					if(@$_POST['oldLastName'] != $newLastName){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE superadmin SET LastName = '$newLastName' WHERE SAdminID = '$SAdminID'") or die("update Error");
						$_SESSION['message2'] = "<p>Last name has been changed.</p>";
					}
					else $_SESSION['message2'] = "";
					
					//if email is changed
					if(@$_POST['oldEmail'] != $newEmail){
						
						$sql = "SELECT * FROM companyadmin WHERE Email = '$newEmail'";
						$qres = mysqli_query($db, $sql); 
						$num_rows=mysqli_num_rows($qres);

						// exists
						if($num_rows > 0){
							//return error
							$_SESSION['message3'] = "<p>Email Address is already in use.</p>";
						}
						// dont exists
						else{
							$sql = "SELECT * FROM superadmin WHERE Email = '$newEmail'";
							$qres = mysqli_query($db, $sql); 
							$num_rows=mysqli_num_rows($qres);
							
							// exists
							if($num_rows > 0){
								//return error
								$_SESSION['message3'] = "<p>Email Address is already in use.</p>";
							}
							// dont exists
							else{
								// exists
								if($num_rows > 0){
									//return error
									$_SESSION['message3'] = "<p>Email Address is already in use.</p>";
								}
								// dont exists
								else{
									$sql = "SELECT * FROM existinguser WHERE Email = '$newEmail'";
									$qres = mysqli_query($db, $sql); 
									$num_rows=mysqli_num_rows($qres);
								
									if($num_rows > 0){
									//return error
									$_SESSION['message3'] = "<p>Email Address is already in use.</p>";
									}
									// dont exists
									else{
										$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
										$result2 = mysqli_query($db,"UPDATE superadmin SET Email = '$newEmail' WHERE SAdminID = '$SAdminID'") or die("update Error");
										$_SESSION['message3'] = "<p>Email Address has been changed.</p>";
									}
								}
							}
						}
					}
					else $_SESSION['message3'] = "";
					
					header('Location: superadmin_ManageAccount.php');
					exit;
				}
			?>
        </div>
    </div>
			<script>
				function confirmDiag(){
					console.log('confirmDiag() executing');
					let result = confirm("Make Changes?");
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


