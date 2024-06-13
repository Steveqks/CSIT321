<?php
session_start();

include_once('superadmin_manageCAdmin_view_functions.php');


if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_edit_specialisation.php');
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

	$_SESSION['teamID'] = $_POST['teamID'];
	$_SESSION['teamName'] = $_POST['teamName'];
	$_SESSION['managerID'] = $_POST['managerID'];
	$_SESSION['sdate'] = $_POST['sdate'];
	$_SESSION['edate'] = $_POST['edate'];

	header('Location: companyadmin_teamManagement_view_delete_edit.php');
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
					<h2>View User Accounts</h2>

  
			<?php     
				
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"SELECT * FROM existinguser WHERE CompanyID = '$companyID'") or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
										<th>User ID</th>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Gender</th>
										<th>Email Address</th>
										<th>Role</th>
										<th>Specialisation ID</th>
										<th>Status</th>
										</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['UserID'] . "</td>" 
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['Gender'] . "</td>"
					."<td>" . $Row['Email'] . "</td>" 
					."<td>" . $Row['SpecialisationID'] . "</td>" 
					."<td>" . $Row['Role'] . "</td>" 
					."<td>" . $Row['Status'] . "</td>";
					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamID' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='teamName' value='" . $Row['LastName'] . "'/>
						<input type='hidden' name='managerID' value='" . $Row['Gender'] . "'/>
						<input type='hidden' name='sdate' value='" . $Row['Email'] . "'/>
						<input type='hidden' name='edate' value='" . $Row['SpecialisationID'] . "'/>
						<input type='hidden' name='edate' value='" . $Row['Role'] . "'/>
						<input type='submit' name='editTeam' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='teamID' value='" . $Row['UserID'] . "'/>
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


