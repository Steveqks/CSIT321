<?php
session_start();

include_once('superadmin_manageCAdmin_view_functions.php');


if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	
	header('Location: companyadmin_edit_specialisation.php');
	exit;
}

if(isset($_POST['deleteTeam']))
{
	$teamID = $_POST['teamID'];
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,	"DELETE FROM team WHERE TeamID = '$teamID' ") or die("Select Error");
	
	$_SESSION['message'] = "Team id\"" .$_POST['teamID']. " ,". $_POST['teamName'] . "\" deleted successfully";
	
	header('Location: companyadmin_teamManagement_view_delete.php');
	exit;
}

if (isset($_POST['editTeam'])) {
	$_SESSION['teamName'] = $_POST['teamName'];
	$_SESSION['teamID'] = $_POST['teamID'];
	header('Location: companyadmin_teamManagement_view_delete_edit.php');
	exit;
}

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
			<h2>View Teams</h2>

  
			<?php     
				
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = 	mysqli_query($db,	
								"SELECT 
									tinfo.MainTeamID,
									tinfo.TeamName,
									CONCAT(e.FirstName, ' ', e.LastName) AS ManagerName,
									COUNT(t.UserID) AS TotalUsers
								FROM 
									team t
								JOIN 
									teaminfo tinfo ON t.MainTeamID = tinfo.MainTeamID
								LEFT JOIN 
									existinguser e ON tinfo.ManagerID = e.UserID
								WHERE 
									tinfo.CompanyID = '$companyID'
								GROUP BY 
									tinfo.MainTeamID,
									tinfo.TeamName, 
									ManagerName;
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
					."<td>" . $Row['ManagerName'] . "</td>" 
					."<td>" . $Row['TotalUsers'] . "</td>";

					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamName' value='" . $Row['TeamName'] . "'/>
						<input type='hidden' name='teamID' value='" . $Row['MainTeamID'] . "'/>
						<input type='submit' name='editTeam' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamID' value='" . $Row['MainTeamID'] . "'/>
						<input type='submit' name='deleteTeam' value='Delete'>
						</form></td>";

					$accountsTable.= "</tr>";
				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
				if(@$_SESSION['message'])
					echo $_SESSION['message'];
			?>
        </div>
    </div>

</body>
</html>


