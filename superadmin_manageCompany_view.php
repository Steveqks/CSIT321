<?php
session_start();

include_once('superadmin_manageCompany_view_functions.php');


if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_edit_specialisation.php');
}

if(isset($_POST['deleteSpecialisation']))
{
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	
	$companyID = $_POST['companyID'];

	$result = mysqli_query($db,	"DELETE FROM company WHERE CompanyID = '$companyID' ") or die("Select Error");
	
	$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" deleted successfully";
	header('Location: superadmin_manageCompany_view.php');
	exit;

}

if(isset($_POST['activateSuspend']))
{
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	
	$companyID = $_POST['companyID'];
	$status = $_POST['Status'];
	
	if($status == 1){
		$result = mysqli_query($db,	"UPDATE company SET Status = 0 WHERE company.CompanyID = '$companyID'") or die("Select Error");
		$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" status set to 0.";
	}
	else if($status == 0){
		$result = mysqli_query($db,	"UPDATE company SET Status = 1 WHERE company.CompanyID = '$companyID'") or die("Select Error");
		$_SESSION['message'] = "Company \"" .$_POST['companyName']. "\" status set to 1.";
	}
	
	
	header('Location: superadmin_manageCompany_view.php');
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
		
  
			<?php     

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
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['CompanyID'] . "</td>" 
						."<td>" . $Row['CompanyName'] . "</td>" 
						."<td>" . $Row['PlanID'] . "</td>" 
						."<td>" . $Row['Status'] . "</td>";
						
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
							<input type='submit' name='activateSuspend' value='Activate/Suspend'>
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
					
					if($_SESSION['message'])
						echo $_SESSION['message'];


			?>
        </div>
    </div>

</body>
</html>


