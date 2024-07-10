<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message'] = '';

	if (isset($_POST['addTeamMember'])) {
		$_SESSION['teamID'] = $_POST['teamID'];
		
		header('Location: companyadmin_teamManagement_view_delete_view_addMember.php');
		exit;
	}

	if (isset($_POST['removemember']) == 'yes') {
		$teamID = $_SESSION['teamID'];
		$userID = $_POST['userID'];
		$fullname = $_POST['fullname'];
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = 	mysqli_query($db, "
									DELETE FROM team
									WHERE UserID = '$userID' AND MainTeamID = $teamID;
									") or die("Select Error");
		
		$_SESSION['message'] = $fullname . " is removed from team";
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh;">
        
        <!-- Left Section (Navigation) -->
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
			

            <!-- Add more content as needed -->
			<?php   

				$companyID = $_SESSION['companyID'];;
				$teamID = $_SESSION['teamID'];
				$teamName = $_SESSION['teamName'];
				
				echo "<h2>View Team: ". $teamName . " </h2>";
				
				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	
					"
					SELECT 
						(@row_number := @row_number + 1) AS RowNumber,
						eu.UserID,
						eu.FirstName,
						eu.LastName,
						s.SpecialisationName
					FROM 
						(SELECT @row_number := 0) AS init
					JOIN 
						team t ON 1=1 
					JOIN 
						existinguser eu ON t.UserID = eu.UserID
					JOIN 
						specialisation s ON eu.SpecialisationID = s.SpecialisationID
					WHERE 
						t.MainTeamID = '$teamID'
					ORDER BY 
						RowNumber;
					") 
				or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
											<th>S/N</th>
											<th>First Name</th>
											<th>Last Name</th>
											<th>Specialisation Name</th>
											</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['RowNumber'] . "</td>" 
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['SpecialisationName'] . "</td>";

	
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='fullname' value='" . $Row['FirstName'] .' '. $Row['LastName']  . "'/>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='removemember' value='yes'/>
						<input type='button' value='Remove' onclick='confirmDiag(this.form)'>
						</form></td>";

					$accountsTable.= "</tr>";

				}
				$accountsTable.= "</table> <br>";
				
				$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamID' value='" . $teamID . "'/>
						<input type='submit' name='addTeamMember' value='ADD TEAM MEMBER'>
						</form></td>";
				echo  $accountsTable;
				
				if(@$_SESSION['message'])
					echo $_SESSION['message'];
			?>
        </div>
    </div>
			<script>
				function confirmDiag(form){
					console.log('confirmDiag() executing');
					let result = confirm("Remove Member?");
					if (result)
					{
						form.submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


