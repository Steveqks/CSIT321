<?php
session_start();

	if(isset($_POST['submitChanges']))
		{
			$newManagerID = $_POST['newManagerID'];
			$newTeamName = $_POST['newTeamName'];
			$managerName = $_POST['managerName'];
			$mTeamID= $_SESSION['mTeamID'];


			//check if there are changes in manager in charge
			if ($_POST['oldManagerID'] != $_POST['newManagerID']){
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				
				$result2 = mysqli_query($db,"UPDATE teaminfo SET ManagerID = '$newManagerID' WHERE MainTeamID = '$mTeamID'") or die("update Error");
				$_SESSION['message1'] = "<p>Team manager has been changed to " . $managerName . "</p>";						
			}
			else $_SESSION['message1'] = "";
			
			// if team name changed
			if(@$_POST['oldTeamName'] != $_POST['newTeamName']){
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

				//check if team name exists
				$result = mysqli_query($db,	"SELECT * FROM teaminfo WHERE TeamName = '$newTeamName' AND CompanyID = '$companyID' ") or die("Select Error");
	
				$num_rows=mysqli_num_rows($result);
				// dont exists
				if($num_rows == 0){
					$result2 = mysqli_query($db,"UPDATE teaminfo SET TeamName = '$newTeamName' WHERE MainTeamID = '$mTeamID'") or die("update Error");
					$_SESSION['message2'] = "<p>Team name has been changed to " . $newTeamName . "</p>";
				}
				// exists
				else{
					$_SESSION['message2'] = "<p>Team name already in use </p>";
				}
			}
			else $_SESSION['message2'] = "";
			
			header('Location: companyadmin_teamManagement_view_delete_edit.php');
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
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
			<h2>Edit Team</h2>
								
  
			<?php     
				
				$companyID = $_SESSION['companyID'];;
				$mTeamID= $_SESSION['mTeamID'];
				
				$thisManagerID = $_SESSION['managerID'];
				
				$ManagerID = '';
				$TeamName = '';
				
				//get selected team data
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"
										SELECT 
											teaminfo.TeamName, 
											CONCAT(existinguser.FirstName, '_', existinguser.LastName) AS ManagerInCharge,
											existinguser.UserID
										FROM 
											teaminfo
										JOIN 
											existinguser ON teaminfo.ManagerID = existinguser.UserID
										WHERE 
											teaminfo.MainTeamID = '$mTeamID'; ") or die("Select Error");

				while ($Row = $result->fetch_assoc()) {
					$ManagerID = $Row['UserID'];
					$ManagerName =	$Row['ManagerInCharge'];
					$TeamName =	$Row['TeamName'];
				}

				// fill and get necessary fields
				$form = "<form action'' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br><br>
						Team Name: <input type='text' name='oldTeamName' value='" . $TeamName . "' readonly><br>
						Manager In Charge: <input type='text' name='' value='" . $ManagerName . "' readonly> <br>
						
						<input type='hidden' name='oldManagerID' value=" . $ManagerID. " readonly> <br>
						</td>
							
						<td style='border: 2px solid black; border-collapse: collapse;'> 
						
						TO
						<br><br>
						Team Name: <input type='text' name='newTeamName' value='" . $TeamName . "' > <br>
						<input type='hidden' name='managerName' value=" . $ManagerName . " > ";
						
				
				$result2 = 	mysqli_query($db, "SELECT CONCAT(FirstName, '_', LastName) AS Fullname, UserID
											FROM existinguser
											WHERE Role = 'Manager' AND CompanyID = $companyID;
											") or die("Select Error");
											
				$form .= "<label for='Manager In Charge'>Manager In Charge:</label>
							<select name='newManagerID' id=''>";		
				while ($Row = $result2->fetch_assoc()) 
				{
					if($Row['UserID'] == $ManagerID)
					{
						$form .= "<option value='" . $Row['UserID'] . "' selected> " . $Row['Fullname'] . " </option>";
					}
			
					else
					{
						$form .= "<option value='" . $Row['UserID'] . "'>" . $Row['Fullname'] . " </option>";
					}
				}
				$form .= "</select> <br><br></td></tr> </table><input type='submit' name='submitChanges' value='Update'></form>";
			
				echo $form;
				
			
			?>
        </div>
    </div>

</body>
</html>


