<?php
	session_start();


	if(isset($_POST['newEmail'])){
		$cAdminID = $_SESSION['cAdminID'];
		
		$newfname = $_POST['newFirstName'];			
		$newlname = $_POST['newLastName'];
		$newemailAdd = $_POST['newEmail'];
		$newPassword = $_POST['newPassword'];
		
		if ($_POST['oldEmail'] != $_POST['newEmail']){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

			//check if email exists.
			$result = mysqli_query($db,	"SELECT * FROM companyadmin WHERE Email = '$newemailAdd'") or die("Select Error");

			$num_rows=mysqli_num_rows($result);
			// dont exists
			if($num_rows == 0){
				$result2 = mysqli_query($db,"UPDATE companyadmin SET Email = '$newemailAdd' WHERE CAdminID = '$cAdminID'") or die("update Error");
				$_SESSION['message1'] = "Email address changed";
			}
			// exists
			else{
				$_SESSION['message1'] = "Email address already exists";
			}
		}
		else $_SESSION['message1'] = "";
		
		// if first name changed
		if(@$_POST['oldFirstName'] != @$newfname){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE companyadmin SET FirstName = '$newfname' WHERE CAdminID = '$cAdminID'") or die("update Error");
			$_SESSION['message2'] = "<p>First Name updated.</p>";
		}
		else $_SESSION['message2'] = "";
		
		// if last name changed
		if(@$_POST['oldLastName'] != @$newlname){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE companyadmin SET LastName = '$newlname' WHERE CAdminID = '$cAdminID'") or die("update Error");
			$_SESSION['message3'] = "<p>Last Name updated.</p>";
		}
		else $_SESSION['message3'] = "";
		
		// if password changed
		if(@$_POST['oldPassword'] != @$newPassword){
			$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
			$result2 = mysqli_query($db,"UPDATE companyadmin SET Password = '$newPassword' WHERE CAdminID = '$cAdminID'") or die("update Error");
			$_SESSION['message4'] = "<p>Password updated.</p>";
		}
		else $_SESSION['message4'] = '';
		
		header('Location: superadmin_manageCAdmin_view_delete_edit.php');
		exit;
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
		
			<h2>Edit Company Admin</h2>
			<?php   
			
				$cAdminID = $_SESSION['cAdminID'];
			
				//get Admin data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"SELECT * FROM companyadmin WHERE CAdminID = '$cAdminID'") or die("Select Error");
			
			
				while($Row = $result->fetch_assoc()){
					$form = "<form action'' id='ModifyAccount' method='POST'>
							<br>
							<table >
							<tr>
								<td style='border: 2px solid black; border-collapse: collapse;'>
							FROM 
							<br><br>
							Company ID: <input type='text' value=" . $Row['CompanyID'] . " readonly><br>
							First Name: <input type='text' name='oldFirstName' value='" . $Row['FirstName'] . "' readonly><br>
							Last Name: <input type='text' name='oldLastName' value='" . $Row['LastName'] . "' readonly> <br>
							Email Address: <input type='text' name='oldEmail' value=" . $Row['Email'] . " readonly> <br>
							Password: <input type='password' name='oldPassword' value=" . $Row['Password'] . " readonly> <br>
							<br>
								</td>
								<td style='border: 2px solid black; border-collapse: collapse;'> 
							TO
							<br><br>
							Company ID: <input type='text' value=" . $Row['CompanyID'] . " readonly> <br>
							First Name: <input type='text' name='newFirstName' value='" . $Row['FirstName'] . "' maxlength='16'> <br>
							Last Name: <input type='text' name='newLastName' value='" . $Row['LastName'] . "' maxlength='16'><br>
							Email Address: <input type='text' name='newEmail' value=" . $Row['Email'] . " maxlength='32'><br>
							Password: <input type='password' name='newPassword' value=" . $Row['Password'] . " > <br>
							<input type='button' value='Update'onclick='confirmDiag();'>
							</form>
								</td>
							</tr>
							</table>
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


