<?php
session_start();

//include_once('superadmin_manageCAdmin_view_functions.php');

if(isset($_POST['deleteUser']))
{
	$userID = $_POST['userID'];
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,	"DELETE FROM existinguser WHERE UserID = '$userID' ") or die("Select Error");
	
	$_SESSION['message1'] = "User ID :" .$userID. ", ". $_POST['fname'] . " ". $_POST['lname'] ." deleted successfully";
	header('Location: companyadmin_ManageUserAccounts_view.php');
	exit;
}

if (isset($_POST['editAccount'])) 
{
	$_SESSION['userID'] = $_POST['userID'];
	$_SESSION['fname'] = $_POST['fname'];
	$_SESSION['lname']  = $_POST['lname'];
	$_SESSION['gender'] = $_POST['gender'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['specialisation'] = $_POST['specialisation'];
	$_SESSION['role'] = $_POST['role'];
	
	
	header('Location: companyadmin_ManageUserAccounts_view_edit.php');
	exit;
}

if (isset($_POST['toggleStatus'])) 
{
	$userID = $_POST['userID'];
	
	if ($_POST['status'] == 0)
	{
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,"UPDATE existinguser SET Status = '1' WHERE UserID = '$userID' ") or die("update Error");
	$_SESSION['message0'] = "User ID :" .$userID. ", ". $_POST['fname'] . " ". $_POST['lname'] ." Status set to Active";
	}
	else
	{
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,"UPDATE existinguser SET Status = '0' WHERE UserID = '$userID' ") or die("update Error");
	$_SESSION['message0'] = "User ID :" .$userID. ", ". $_POST['fname'] . " ". $_POST['lname'] ." Status set to Suspended";
	}
	
	header('Location: companyadmin_ManageUserAccounts_view.php');
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
				<a href="companyadmin_homepage.php">Home</a>
				<a href="companyadmin_ManageAccount.php">Manage Account</a>
				<a href="companyadmin_ManageUserAccounts_create.php">Manage User Accounts > Create</a>
				<a href="companyadmin_ManageUserAccounts_view.php">Manage User Accounts > View</a>
				<a href="companyadmin_specialisation_create.php">Manage Specialisation > Create </a>
				<a href="companyadmin_specialisation_view_delete.php">Manage Specialisation > View</a>
				<a href="companyadmin_teamManagement_create.php">Manage Team > Create </a>
				<a href="companyadmin_teamManagement_view_delete.php">Manage Team > View</a>
				<a href="Logout.php">Logout</a>

			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
					<h2>View User Accounts</h2>

  
			<?php     
				echo @$_SESSION['message0'];
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
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
						<input type='hidden' name='gender' value='" . $Row['Gender'] . "'/>
						<input type='hidden' name='email' value='" . $Row['Email'] . "'/>
						<input type='hidden' name='specialisation' value='" . $Row['SpecialisationID'] . "'/>
						<input type='hidden' name='role' value='" . $Row['Role'] . "'/>
						<input type='submit' name='editAccount' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='status' value='" . $Row['Status'] . "'/>
						<input type='submit' name='toggleStatus' value='Activate/Suspend'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
						<input type='submit' name='deleteUser' value='Delete'>
						</form></td>";
					$accountsTable.= "</tr>";
				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
				if(@$_SESSION['message1'])
					echo $_SESSION['message1'];
			?>
        </div>
    </div>

</body>
</html>


