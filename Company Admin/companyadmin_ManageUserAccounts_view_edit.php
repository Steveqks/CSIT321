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
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
			<h2>Edit User Account</h2>

			<?php   
				
				$companyID = $_SESSION['companyID'];;
			
				
				$ManagerID = '';
				$TeamName = '';
				
				$userID = $_SESSION['userID'];
				
				//get selected team data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"
										SELECT 
											eu.FirstName,
											eu.LastName,
											eu.Gender,
											eu.Email,
											s.SpecialisationID,
											s.SpecialisationName,
											eu.Role,
											eu.Status,
											eu.UserID
										FROM 
											existinguser eu
										JOIN 
											specialisation s ON eu.SpecialisationID = s.SpecialisationID
										WHERE 
											eu.CompanyID = '$companyID' AND eu.UserID = '$userID';
										 ") or die("Select Error");
				
				while ($Row = $result->fetch_assoc()) {
					$UserID = $Row['UserID'];
					$SpecialisationName =	$Row['SpecialisationName'];
					$Role =	$Row['Role'];
					$Status =	$Row['Status'];
				// fill and get necessary fields
				$form = "<form action'' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br><br>
						First Name: <input type='text' name='oldFirstName' value='" . $Row['FirstName'] . "' readonly><br>
						Last Name: <input type='text' name='oldLastName' value='" . $Row['LastName'] . "' readonly> <br>							
						Gender: <input type='text' name='oldGender' value=" . $Row['Gender'] . " readonly> <br>
						Email: <input type='text' name='oldEmail' value=" . $Row['Email'] . " readonly> <br>
						Specialisation: <input type='text' name='oldSName' value=" . $Row['SpecialisationName'] . " readonly> <br>
							<input type='hidden' name='oldSID' value=" . $Row['SpecialisationID'] . " readonly>
						Role: <input type='text' name='oldRole' value=" . $Row['Role'] . " readonly> <br>
						Status: <input type='text' name='oldStatus' value=" . $Row['Status'] . " readonly> <br>
						<br>
							
						<td style='border: 2px solid black; border-collapse: collapse;'> 
						
						TO
						<br><br>
						First Name: <input type='text' name='newFirstName' value='" . $Row['FirstName'] . "' ><br>
						Last Name: <input type='text' name='newLastName' value='" . $Row['LastName'] . "' > <br>							
						Gender: <input type='text' name='newGender' value=" . $Row['Gender'] . " > <br>
						Email: <input type='text' name='newEmail' value=" . $Row['Email'] . " > <br>";
				}
				
				$result2 = 	mysqli_query($db, "SELECT * FROM `specialisation` WHERE CompanyID = '82';
											") or die("Select Error");
											
				$form .= "<label for='Specialisation'>Specialisation:</label>
							<select name='newSID' id=''>";		
				while ($Row = $result2->fetch_assoc()) 
				{
					if($Row['SpecialisationName'] == $SpecialisationName)
					{
						$form .= "<option value='" . $Row['SpecialisationID'] . "' selected> " . $Row['SpecialisationName'] . " </option>";
					}
			
					else
					{
						$form .= "<option value='" . $Row['SpecialisationID'] . "'>" . $Row['SpecialisationName'] . " </option>";
					}
				}
				
				$form .= "	</select><br>
							<label for='Role'>Role:</label>
							<select name='newRole' id=''>";
							
				if($Role == "Manager") $form .= "<option value='Manager' selected>Manager</option>
												<option value='FT'>FT</option>
												<option value='PT'>PT</option>";
				if($Role == "FT") $form .= "<<option value='Manager'>Manager</option>
												<option value='FT' selected>FT</option>
												<option value='PT'>PT</option>";
				if($Role == "PT") $form .= "<<option value='Manager'>Manager</option>
												<option value='FT'>FT</option>
												<option value='PT' selected>PT</option>";

				$form .= "</select>
						<br>
							Status: <input type='text' name='' value=" . $Status . " readonly><br>";
						
				$form .= "</select><input type='submit' name='submitChanges' value='Update'> </td></tr> </table></form>";
			
				echo $form;
				
				
				echo $_SESSION['message1'];
				echo $_SESSION['message2'];
				echo $_SESSION['message3'];
				echo $_SESSION['message4'];
				echo $_SESSION['message5'];
				echo $_SESSION['message6'];
				
				if(isset($_POST['submitChanges'])){
					
					$newFirstName = $_POST['newFirstName'];
					$newLastName = $_POST['newLastName'];
					$newGender = $_POST['newGender'];
					$newEmail = $_POST['newEmail'];
					$newSID = $_POST['newSID'];
					$newRole = $_POST['newRole'];
					
					//check if there are changes in first name
					if ($_POST['newFirstName'] != $_POST['oldFirstName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						
						$result = mysqli_query($db,"UPDATE existinguser SET FirstName = '$newFirstName' WHERE UserID = '$userID'") or die("update Error");
						$_SESSION['message1'] = "<p>First Name has been changed";
					}
					else $_SESSION['message1'] = "";
					
					// check if there are changes in last name
					if ($_POST['newLastName'] != $_POST['oldLastName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						
						$result = mysqli_query($db,"UPDATE existinguser SET LastName = '$newLastName' WHERE UserID = '$userID'") or die("update Error");
						$_SESSION['message2'] = "<p>Last Name has been changed";
					}
					else $_SESSION['message2'] = "";
					
					// check if there are changes in gender
					if(@$_POST['newGender'] != $_POST['oldGender']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET Gender = '$newGender' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message3'] = "<p>Gender has been changed.</p>";
					}
					else $_SESSION['message3'] = "";
										
					// check if there are changes in email
					if(@$_POST['newEmail'] != $_POST['oldEmail']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET Email = '$newEmail' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message4'] = "<p>Email has been changed.</p>";

					}
					else $_SESSION['message4'] = "";
					
					// check if there are changes in specialisation
					if(@$_POST['newSID'] != $_POST['oldSID']){
						
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET SpecialisationID = '$newSID' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message5'] = "<p >Specialisation has been changed.</p>";
						
					}
					else $_SESSION['message5'] = "";
					
					// check if there are changes in role
					if(@$_POST['newRole'] != $_POST['oldRole']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE existinguser SET Role = '$newRole' WHERE UserID = '$userID' ") or die("update Error");
						$_SESSION['message6'] = "<p>Role has been changed.</p>" ;
					}
					else $_SESSION['message6'] = '';
					
					header('Location: companyadmin_ManageUserAccounts_view_edit.php');
					exit;
				}
			?>
        </div>
    </div>

</body>
</html>


