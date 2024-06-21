<?php
session_start();

include_once('companyadmin_specialisation_viewdelete_functions.php');

if(isset($_POST['deleteSpecialisation']))
{
	$delete = new userAccount();
	$delete->deleteSpecialisation($_POST['specialisationID']);
	
	$_SESSION['message'] = "Specialisation \"" . $_POST['specialisationName'] . "\" deleted successful";
	header('Location: companyadmin_specialisation_view_delete.php');
	exit();
}

if (isset($_POST['editSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	$_SESSION['message'] = '';
	header('Location: companyadmin_specialisation_edit.php');
	exit();
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
			<h2>View Specialisation</h2>

				<?php   
					$companyID = $_SESSION['companyID'];;
				

					$view = new userAccount();
					$qres = $view->viewSpecialisation($companyID);
					
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>SpecialisationID</th>
												<th>Specialisation Name</th>
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n";
						$accountsTable .= "<td>" . $Row['SpecialisationID'] . "</td>";
						$accountsTable .= "<td>" . $Row['SpecialisationName'] . "</td>";
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
							<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
							<input type='submit' name='editSpecialisation' value='Edit'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
							<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
							<input type='submit' name='deleteSpecialisation' value='Delete'>
							</form></td>";
							

						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;

					if(@$_SESSION['message']) echo $_SESSION['message'];

				?>
	


        </div>
    </div>

</body>
</html>


