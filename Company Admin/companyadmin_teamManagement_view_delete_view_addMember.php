<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	if (isset($_POST['AddMember'])) {

		$userID = $_POST['userID'];
		$teamID = $_SESSION['teamID'];
		$fullname = $_POST['fullname'];

		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = 	mysqli_query($db, "
			INSERT INTO team(TeamID, MainTeamID, UserID)
			VALUES (Null, '$teamID', '$userID');
				") or die("Select Error");
								
		$_SESSION['message'] = " ";
		$_SESSION['message1'] = $fullname . " is added to team";
		header('Location: companyadmin_teamManagement_view_delete_view_addMember.php');
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
			

            <!-- Add more content as needed -->
			<?php   

				$companyID = $_SESSION['companyID'];;
				$teamID = $_SESSION['teamID'];
				$teamName = $_SESSION['teamName'];
				
				echo "<h2>Add Member to Team: ". $teamName . " </h2>";
				
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = 	mysqli_query($db, "
					SELECT 
						eu.UserID,
						eu.FirstName,
						eu.LastName,
						s.SpecialisationName
					FROM 
						existinguser eu
					JOIN 
						specialisation s ON eu.SpecialisationID = s.SpecialisationID
					WHERE 
						eu.CompanyID = '$companyID'
						AND eu.Role IN ('PT', 'FT')
						AND NOT EXISTS (
							SELECT 1 
							FROM team t 
							WHERE t.UserID = eu.UserID AND t.MainTeamID = $teamID
						);

						") 
							or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Specialisation Name</th>
											</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['SpecialisationName'] . "</td>";

	
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='fullname' value='" . $Row['FirstName'] .' '. $Row['LastName']  . "'/>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='submit' name='AddMember' value='Add'>
						</form></td>";

					$accountsTable.= "</tr>";

				}
				
				//if zero rows returned
				if (!@$hasRows) {
					$accountsTable .= "<tr>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "</tr>";
				}
				$accountsTable.= "</table> <br>";
				
			
				echo  $accountsTable;
				
				if(@$_SESSION['message1'])
					echo $_SESSION['message1'];
			?>
        </div>
    </div>

</body>
</html>


