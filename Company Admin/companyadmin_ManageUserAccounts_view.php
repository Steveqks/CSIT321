<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	if(isset($_POST['delete'])=='yes')
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
					<h2>View User Accounts</h2>

  
			<?php     
				echo @$_SESSION['message0'];
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	"SELECT 
												eu.UserID,
												eu.FirstName,
												eu.LastName,
												eu.Gender,
												eu.Email AS EmailAddress,
												s.SpecialisationName,
												s.SpecialisationID,
												eu.Role,
												eu.Status
											FROM 
												existinguser eu
											JOIN 
												specialisation s ON eu.SpecialisationID = s.SpecialisationID
											WHERE 
												eu.CompanyID = '$companyID';
											") or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Gender</th>
										<th>Email Address</th>
										<th>Specialisation</th>
										<th>Role</th>
										<th>Status</th>
										</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" 
					."<td>" . $Row['Gender'] . "</td>"
					."<td>" . $Row['EmailAddress'] . "</td>" 
					."<td>" . $Row['SpecialisationName'] . "</td>" 
					."<td>" . $Row['Role'] . "</td>" 
					."<td>" . $Row['Status'] . "</td>";
					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='submit' name='editAccount' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='status' value='" . $Row['Status'] . "'/>
						<input type='submit' name='toggleStatus' value='Activate/Suspend'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
						<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
						<input type='hidden' name='delete' value='yes'/>
						<input type='button' value='Delete' onclick='confirmDiag(event, this.form)'>
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
			<script>
				function confirmDiag(event, form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete User?");
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


