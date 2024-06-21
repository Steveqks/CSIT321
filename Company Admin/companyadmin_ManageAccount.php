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
			<a href="companyadmin_homepage.php">Home</a>
			<a href="companyadmin_ManageAccount.php">Manage Account</a>
			<a href="companyadmin_ManageUserAccounts_create.php">Manage User Accounts > Create</a>
			<a href="companyadmin_ManageUserAccounts_view.php">Manage User Accounts > View</a>
			<a href="companyadmin_specialisation_create.php">Manage Specialisation > Create </a>
			<a href="companyadmin_specialisation_view_delete.php">Manage Specialisation > View</a>
			<a href="companyadmin_teamManagement_create.php">Manage Team > Create </a>
			<a href="companyadmin_teamManagement_view_delete.php">Manage Team > View</a>
			<a href="Logout.php">Logout</a>

			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Manage Account</h2>
			<?php   
				$cadminID = $_SESSION['cadminID'];
				
				//get Company Admin data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"SELECT * FROM companyadmin WHERE CAdminID = '$cadminID'") or die("Select Error");
			
				while($Row = $result->fetch_assoc()){
				$form = "<form action'' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br>
						
						First Name: <input type='text' name='oldFirstName' value=" . $Row['FirstName'] . " readonly><br>
						Last Name: <input type='text' name='oldLastName' value=" . $Row['LastName'] . " readonly> <br>
						Email Address: <input type='text' name='oldEmail' value=" . $Row['Email'] . " readonly> <br>
						<br></td>
							
							<td style='border: 2px solid black; border-collapse: collapse;'> 
						TO
						<br>
						First Name: <input type='text' name='newFirstName' value=" . $Row['FirstName'] . " > <br>
						Last Name: <input type='text' name='newLastName' value=" . $Row['LastName'] . "><br>
						Email Address: <input type='text' name='newEmail' value=" . $Row['Email'] . "><br>
						
						<input type='submit' name='submitChanges' value='Update'>
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
				
				if(isset($_POST['submitChanges'])){
					$newFirstName = $_POST['newFirstName'];			
					$newLastName = $_POST['newLastName'];			
					$newEmail = $_POST['newEmail'];			
					
					//check if there are changes in first name
					if ($_POST['oldFirstName'] != $_POST['newFirstName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
							$result2 = mysqli_query($db,"UPDATE companyadmin SET FirstName = '$newFirstName' WHERE CAdminID = '$cadminID' ") or die("update Error");
							$_SESSION['message1'] = "<p style='color: green;'>First name has been changed.</p>";
						
					}
					else $_SESSION['message1'] = "";
					
					//check if there are changes in last name
					if(@$_POST['oldLastName'] != $newLastName){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET LastName = '$newLastName' WHERE CAdminID = '$cadminID'") or die("update Error");
						$_SESSION['message2'] = "<p style='color: green;'>Last name has been changed.</p>";
					}
					else $_SESSION['message2'] = "";
					
					// if last name changed
					if(@$_POST['oldEmail'] != $newEmail){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE companyadmin SET Email = '$newEmail' WHERE CAdminID = '$cadminID'") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Email Address has been changed.</p>";
					}
					else $_SESSION['message3'] = "";
					
					header('Location: companyadmin_ManageAccount.php');
					
				}
			?>
        </div>
    </div>

</body>
</html>

