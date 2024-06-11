<?php
session_start();

if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_edit_specialisation.php');
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
		
  
			<?php     
				include_once('superadmin_manageCompany_view_functions.php');

				$view = new userAccount();
						$qres = $view->viewCompany();
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>Company ID</th>
												<th>Company Name</th>
												<th>Subcription Plan</th>
												<th>Status</th>
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n";
						$accountsTable .= "<td>" . $Row['CompanyID'] . "</td>";
						$accountsTable .= "<td>" . $Row['CompanyName'] . "</td>";
						$accountsTable .= "<td>" . $Row['PlanID'] . "</td>";
						$accountsTable .= "<td>" . $Row['Status'] . "</td>";
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='companyName' value='" . $Row['CompanyName'] . "'/>
							<input type='hidden' name='plantID' value='" . $Row['PlanID'] . "'/>
							<input type='hidden' name='Status' value='" . $Row['Status'] . "'/>
							<input type='submit' name='editSpecialisation' value='Edit'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='companyName' value='" . $Row['CompanyName'] . "'/>
							<input type='hidden' name='plantID' value='" . $Row['PlanID'] . "'/>
							<input type='hidden' name='Status' value='" . $Row['Status'] . "'/>
							<input type='submit' name='deleteSpecialisation' value='Delete'>
							</form></td>";
							

						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					

					if(isset($_POST['deleteSpecialisation']))
					{
						$delete = new userAccount();
						
						$delete->deleteSpecialisation($_POST['specialisationID']);
						header('Location: companyadmin_viewdelete_specialisation.php');
					}
			?>
        </div>
    </div>

</body>
</html>


