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

			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Edit User Account</h2>

			<?php   
				
				$companyID = $_SESSION['companyID'];;
			
				$form = "<form action'' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						<b>FROM: </b>
						<br>
							First Name: <input type='text' name='oldfname' value=" . $_SESSION['fname'] . " readonly><br>
							Last Name: <input type='text' name='oldlname' value=" . $_SESSION['lname'] . " readonly> <br>
							Gender: <input type='text' name='oldgender' value=" . $_SESSION['gender'] . " readonly> <br>
							Email: <input type='text' name='oldemail' value=" . $_SESSION['email'] . " readonly> <br>
							Specialisation: <input type='text' name='oldspecialisation' value=" . $_SESSION['specialisation'] . " readonly><br>
							Role: <input type='text' name='oldrole' value=" . $_SESSION['role'] . " readonly><br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'> 
						<b>TO:</b>
						<br>
							First Name: <input type='text' name='newfname' value=" . $_SESSION['fname'] . " > <br>
							Last Name: <input type='text' name='newlname' value=" . $_SESSION['lname'] . "><br>
							Gender: <input type='text' name='newgender' value=" . $_SESSION['gender'] . "><br>
							Email: <input type='text' name='newemail' value=" . $_SESSION['email'] . "><br>
							Specialisation: <input type='text' name='newspecialisation' value=" . $_SESSION['specialisation'] . "><br>
							Role: <input type='text' name='newrole' value=" . $_SESSION['role'] . " ><br>
						<input type='submit' name='submitChanges' value='Update'>
						</form>
							</td>
						</tr>
						</table>
							";
				echo $form;
				
				echo $_SESSION['message1'];
				echo $_SESSION['message2'];
				echo $_SESSION['message3'];
				echo $_SESSION['message4'];
				echo $_SESSION['message5'];
				echo $_SESSION['message6'];
				
				if(isset($_POST['submitChanges'])){
					$userID = $_SESSION['userID'];
					$newfname = $_POST['newfname'];
					$newlname = $_POST['newlname'];
					$newgender = $_POST['newgender'];
					$newemail = $_POST['newemail'];
					$newspecialisation = $_POST['newspecialisation'];
					$newrole = $_POST['newrole'];
					
					//check if there are changes in email
					if ($_POST['oldemail'] != $_POST['newemail']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

						//check if email already exists
						$result = mysqli_query($db,	"SELECT * FROM existinguser WHERE Email = '$newEmail' ") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result);
						// dont exists
						if($num_rows == 0){
							$result2 = mysqli_query($db,"UPDATE existinguser SET Email = '$newEmail' WHERE UserID = 'userID") or die("update Error");
							$_SESSION['message1'] = "Email Address has been changed";
							$_SESSION['email'] = $newemail;
						}
						// exists
						else{
							$_SESSION['message1'] = "Email Address already exists";
						}
					}
					else $_SESSION['message1'] = "";
					
					// if first name changed
					if(@$_POST['oldfname'] != $_POST['newfname']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET FirstName = '$newfname' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message2'] = "<p style='color: green;'>First Name has been changed.</p>";
						$_SESSION['fname'] = $newfname;
					}
					else $_SESSION['message2'] = "";
					
					// if last name changed
					if(@$_POST['oldlname'] != $_POST['newlname']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET LastName = '$newlname' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Last Name has been changed.</p>";
						$_SESSION['lname'] = $newlname;
					}
					else $_SESSION['message3'] = "";
										
					// if gender changed
					if(@$_POST['oldgender'] != $_POST['newgender']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET Gender = '$newgender' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Last Name has been changed.</p>";
						$_SESSION['gender'] = $newgender;

					}
					else $_SESSION['message4'] = "";
					
					// if specialisation changed
					if(@$_POST['oldspecialisation'] != $_POST['newspecialisation']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET SpecialisationID = '$newspecialisation' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Last Name has been changed.</p>";
						$_SESSION['specialisation'] = $newspecialisation;
					}
					else $_SESSION['message5'] = "";
					
					// if role changed
					if(@$_POST['oldrole'] != $_POST['newrole']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET Role = '$newrole' WHERE UserID = 'userID'") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Last Name has been changed.</p>";
						$_SESSION['role'] = $newrole;
					}
					else $_SESSION['message6'] = "";
					
					header('Location: companyadmin_ManageUserAccounts_view_edit.php');
					exit;
				}
			?>
        </div>
    </div>

</body>
</html>


