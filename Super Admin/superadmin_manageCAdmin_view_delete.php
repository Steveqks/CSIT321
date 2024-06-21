<?php
session_start();

include_once('superadmin_manageCAdmin_view_functions.php');


if (isset($_POST['submitSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	header('Location: companyadmin_edit_specialisation.php');
	exit;
}

if(isset($_POST['deleteCAdmin']))
{
	$cAdminID = $_POST['cAdminID'];
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,	"DELETE FROM companyadmin WHERE CAdminID = '$cAdminID' ") or die("Select Error");
	
	$_SESSION['message'] = "Company Admin \"" .$_POST['fname']. " ". $_POST['lname'] . "\" deleted successfully";
	header('Location: superadmin_manageCAdmin_view_delete.php');
	exit;
}

if(isset($_POST['activateSuspend']))
{
	$cAdminID = $_POST['cAdminID'];
	$status = $_POST['status'];
	
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	
	if($status == 1){
		$result = mysqli_query($db,	"UPDATE companyadmin SET Status = 0 WHERE CAdminID = '$cAdminID'") or die("Select Error");
		$_SESSION['message'] = "Company Admin \"" .$cAdminID. "\" status set to 0.";
	}
	else if($status == 0){
		$result = mysqli_query($db,	"UPDATE companyadmin SET Status = 1 WHERE CAdminID = '$cAdminID'") or die("Select Error");
		$_SESSION['message'] = "Company Admin \"" .$cAdminID. "\" status set to 1.";
	}
	header('Location: superadmin_manageCAdmin_view_delete.php');
	exit;
}

if (isset($_POST['editCAdmin'])) {

	$_SESSION['cAdminID'] = $_POST['cAdminID'];
	$_SESSION['fname'] = $_POST['fname'];
	$_SESSION['lname'] = $_POST['lname'];
	$_SESSION['emailAdd'] = $_POST['emailAdd'];
	$_SESSION['message'] = '';
	header('Location: superadmin_manageCAdmin_view_delete_edit.php');
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
			  <a href="superadmin_homepage.php">Home</a>
			  <a href="superadmin_ManageAccount.php">Manage Account</a>
			  <a href="superadmin_manageCompany_create.php">Manage Company > Create Company </a>
			  <a href="superadmin_manageCompany_view.php">Manage Company > View Company </a>
			  <a href="superadmin_manageCAdmin_approve_unreg_user.php">Approve New Company (Create New Company & Company Admin)</a>
			  <a href="superadmin_manageCAdmin_create.php">Manage Company Admin > Create Company Admin</a>
			  <a href="superadmin_manageCAdmin_view_delete.php">Manage Company Admin > View Company Admin</a>
			  <a href="Logout.php">Logout</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
		
  			<h2>View Company Admins</h2>

			<?php     

				$view = new userAccount();
						$qres = $view->viewCAdmin();
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>Company Admin ID</th>
												<th>Company ID</th>
												<th>First Name</th>
												<th>Last Name</th>
												<th>Email</th>
												<th>Status</th>
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['CAdminID'] . "</td>" 
						."<td>" . $Row['CompanyID'] . "</td>" 
						."<td>" . $Row['FirstName'] . "</td>" 
						."<td>" . $Row['LastName'] . "</td>"
						."<td>" . $Row['Email'] . "</td>"
						."<td>" . $Row['Status'] . "</td>";
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='companyID' value='" . $Row['CompanyID'] . "'/>
							<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
							<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
							<input type='hidden' name='emailAdd' value='" . $Row['Email'] . "'/>
							<input type='submit' name='editCAdmin' value='Edit'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='status' value='" . $Row['Status'] . "'/>
							<input type='submit' name='activateSuspend' value='Activate/Suspend'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='cAdminID' value='" . $Row['CAdminID'] . "'/>
							<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
							<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
							<input type='submit' name='deleteCAdmin' value='Delete'>
							</form></td>";
						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					if(@$_SESSION['message'])
					{
						echo $_SESSION['message'];
					}


			?>
        </div>
    </div>

</body>
</html>

