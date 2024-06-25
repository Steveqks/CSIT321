<?php
session_start();

//include_once('superadmin_manageCAdmin_view_functions.php');

if(isset($_POST['deleteEntry']))
{
	$calendarID = $_POST['calendarID'];
	$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
	$result = mysqli_query($db,	"DELETE FROM calendar WHERE CalendarID = '$calendarID' ") or die("Select Error");
	
	$_SESSION['message1'] = "Calendar entry for " .$_POST['date']. ", ". $_POST['dateName'] ." deleted successfully";
	header('Location: companyadmin_ManageCalendar_view.php');
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
					<h2>View Calendar Entries</h2>

  
			<?php     
				echo @$_SESSION['message0'];
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				
				mysqli_query($db, "SET @row_number = 0;") or die("Error setting row number");

				$result = mysqli_query($db,	"SELECT 
												@row_number := @row_number + 1 AS SN,
												calendar.*
											FROM 
												calendar
											WHERE 
												CompanyID = '$companyID';
											") or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
										<th>S/n</th>
										<th>Date Name</th>
										<th>Date</th>
										</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['SN'] . "</td>" 
					."<td>" . $Row['DateName'] . "</td>" 
					."<td>" . $Row['Date'] . "</td>";
					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='dateName' value='" . $Row['DateName'] . "'/>
						<input type='hidden' name='date' value='" . $Row['Date'] . "'/>
						<input type='submit' name='editAccount' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='calendarID' value='" . $Row['CalendarID'] . "'/>
						<input type='hidden' name='dateName' value='" . $Row['DateName'] . "'/>
						<input type='hidden' name='date' value='" . $Row['Date'] . "'/>
						<input type='submit' name='deleteEntry' value='Delete'>
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


