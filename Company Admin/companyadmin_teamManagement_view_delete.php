<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	if (isset($_POST['viewTeam'])) {
		$_SESSION['teamName'] = $_POST['teamName'];
		$_SESSION['teamID'] = $_POST['teamID'];
		header('Location: companyadmin_teamManagement_view_delete_view.php');
		exit;
	}

	if (isset($_POST['editTeam'])) {
		$_SESSION['mTeamID'] = $_POST['mTeamID'];
		$_SESSION['managerID'] = $_POST['managerID'];
		header('Location: companyadmin_teamManagement_view_delete_edit.php');
		exit;
	}

	if(isset($_POST['deleteTeam']))
	{
		$teamID = $_POST['teamID'];
		$totalUser = $_POST['totalUser'];
		$teamName = $_POST['teamName'];
		
		//remove team members from team first, then delete team
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = 	mysqli_query($db, "
									DELETE FROM team
									WHERE MainTeamID = '$teamID';
									") or die("Select Error");
									
		$result2 = 	mysqli_query($db, "
									DELETE FROM teaminfo
									WHERE MainTeamID = '$teamID';
									") or die("Select Error");
		
		$_SESSION['message'] = " ";
		$_SESSION['message1'] = " ";
		$_SESSION['message2'] =  $totalUser . " users is removed from team \"". $teamName . "\", Team \"". $teamName ."\" deleted.";
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
			<h2>View Teams</h2>

  
			<?php     
				
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = 	mysqli_query($db,	
								"SELECT 
									ti.MainTeamID,
									ti.TeamName,
									eu.FirstName,
									eu.LastName,
                                    eu.UserID,
									COUNT(t.TeamID) AS TotalUsers
								FROM 
									teaminfo ti
								LEFT JOIN 
									existinguser eu ON ti.ManagerID = eu.UserID
								LEFT JOIN 
									team t ON ti.MainTeamID = t.MainTeamID
								WHERE 
									ti.CompanyID = '82'
								GROUP BY 
									ti.MainTeamID, ti.TeamName, eu.FirstName, eu.LastName;
								") 
							or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
											<th>Team Name</th>
											<th>Manager In Charge</th>
											<th>Total Staff In Team</th>
											</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['TeamName'] . "</td>" 
					."<td>" . $Row['FirstName'] . "_" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['TotalUsers'] . "</td>";

					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamName' value='" . $Row['TeamName'] . "'/>
						<input type='hidden' name='teamID' value='" . $Row['MainTeamID'] . "'/>
						<input type='submit' name='viewTeam' value='View'>
						</form></td>";
						
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='managerID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='mTeamID' value='" . $Row['MainTeamID'] . "'/>
						<input type='submit' name='editTeam' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='totalUser' value='" . $Row['TotalUsers'] . "'/>
						<input type='hidden' name='teamName' value='" . $Row['TeamName'] . "'/>
						<input type='hidden' name='teamID' value='" . $Row['MainTeamID'] . "'/>
						<input type='submit' name='deleteTeam' value='Delete'>
						</form></td>";

					$accountsTable.= "</tr>";
				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
				if(@$_SESSION['message2'])
					echo $_SESSION['message2'];
			?>
        </div>
    </div>

</body>
</html>


