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
			  <a href="#">Home</a>
			  <a href="#">Link 1</a>
			  <a href="#">Link 2</a>
			  <a href="#">Link 3</a>
			  <a href="#">Link 4</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
            <!-- Add more content as needed -->
			<?php   
				
				$companyID = $_SESSION['companyID'];;
			
				echo "Edit Company Admin:<br>";
				$form = "<form action'' method='POST'>
						<br>
						<table >
						<tr>
							<td style='border: 2px solid black; border-collapse: collapse;'>
						FROM 
						<br>
						Team id: <input type='text' value=" . $_SESSION['teamID'] . " readonly><br>
						Team Name: <input type='text' name='oldTeamName' value=" . $_SESSION['teamName'] . " readonly> <br>
						Manager: <input type='text' name='oldManagerID' value=" . $_SESSION['managerID'] . " readonly> <br>
						Start Date: <input type='text' name='oldSDate' value=" . $_SESSION['sdate'] . " readonly> <br>
						End Date: <input type='text' name='oldEDate' value=" . $_SESSION['edate'] . " readonly><br>
						<br>
							</td>
							<td style='border: 2px solid black; border-collapse: collapse;'> 
						TO
						<br>
						Team id: <input type='text' name='teamID' value=" . $_SESSION['teamID'] . " readonly> <br>
						Team Name: <input type='text' name='newTeamName' value=" . $_SESSION['teamName'] . "><br>
						Manager: <input type='text' name='newManagerID' value=" . $_SESSION['managerID'] . "><br>
						Start Date: <input type='date' name='newSDate' value=" . $_SESSION['sdate'] . "><br>
						End Date: <input type='date' name='newEDate' value=" . $_SESSION['edate'] . "><br>
						<input type='submit' name='submitChanges' value='Update'>
						</form>
							</td>
						</tr>
						</table>
							";
				echo $form;
				
				echo @$_SESSION['message1'];
				echo @$_SESSION['message2'];
				echo @$_SESSION['message3'];
				echo @$_SESSION['message4'];
				
				if(isset($_POST['submitChanges'])){
					$teamID = $_POST['teamID'];			
					$newTeamName = $_POST['newTeamName'];			
					$newManagerID = $_POST['newManagerID'];			
					$newSDate = $_POST['newSDate'];
					$newEDate = $_POST['newEDate'];
					
					//check if there are changes in team name
					if ($_POST['oldTeamName'] != $_POST['newTeamName']){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");

						//check if team name already exists
						$result = mysqli_query($db,	"SELECT * FROM team WHERE TeamName = '$newTeamName' AND CompanyID = $companyID") or die("Select Error");
			
						$num_rows=mysqli_num_rows($result);
						// dont exists
						if($num_rows == 0){
							$result2 = mysqli_query($db,"UPDATE team SET TeamName = '$newTeamName' WHERE TeamID = '$teamID'") or die("update Error");
							$_SESSION['message1'] = "Team name has been changed";
							$_SESSION['teamName'] = $newTeamName;
						}
						// exists
						else{
							$_SESSION['message1'] = "Team name already exists";
						}
					}
					else $_SESSION['message1'] = "";
					
					// if there are changes in manager
					if(@$_POST['oldManagerID'] != $newManagerID){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE team SET ManagerID = '$newManagerID' WHERE TeamID = '$teamID'") or die("update Error");
						$_SESSION['message2'] = "<p style='color: green;'>Manager has been changed.</p>";
						$_SESSION['managerID'] = $newManagerID;
					}
					else $_SESSION['message2'] = "";
					
					// if last name changed
					if(@$_POST['oldSDate'] != $newSDate){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE team SET StartDate = '$newSDate' WHERE TeamID = '$teamID'") or die("update Error");
						$_SESSION['message3'] = "<p style='color: green;'>Start Date has been changed.</p>";
						$_SESSION['sdate'] = $newSDate;
					}
					else $_SESSION['message3'] = "";
					
					header('Location: companyadmin_teamManagement_view_delete_edit.php');
					
					// if last name changed
					if(@$_POST['oldEDate'] != $newEDate){
						$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
						$result2 = mysqli_query($db,"UPDATE team SET EndDate = '$newEDate' WHERE TeamID = '$teamID'") or die("update Error");
						$_SESSION['message4'] = "<p style='color: green;'> End Date has been changed.</p>";
						$_SESSION['edate'] = $newEDate;
					}
					else $_SESSION['message4'] = "";
					
					header('Location: companyadmin_teamManagement_view_delete_edit.php');
					exit;
				}
			?>
        </div>
    </div>

</body>
</html>


