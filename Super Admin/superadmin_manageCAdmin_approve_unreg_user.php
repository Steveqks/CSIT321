<?php
session_start();
	
	include_once('superadmin_manageCAdmin_approve_unreg_user_functions.php');
	
	$_SESSION['message'] = '';
	
	if(isset($_POST['approveAccount']))
	{
		$aprrove = new userAccount();

		switch ($aprrove->approveAccount($_POST['fname'], $_POST['companyUEN'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['cname'], $_POST['planID'])){
			//company exists
			case 1 : $_SESSION['message'] = "company already exists in system"; 
			break;
			
			//company admin exists
			case 2 : $_SESSION['message'] = "company admin email already exists in system"; 
			break;
			
			//create both
			case 3 : $_SESSION['message'] = "company and company admin created."; 
			break;
			
			default : $_SESSION['message'] = "nothing happened"; 
			break;
			
		}

	}

	if(isset($_POST['deleteEntry']))
	{
		$applicationID = $_POST['applicationID'];

		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = mysqli_query($db,	"DELETE FROM unregisteredusers  WHERE ApplicationID = '$applicationID' ") or die("Select Error");
		
		$_SESSION['message'] = "Application  for \"" .$_POST['cname']. "\" deleted successfully";
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh;">
        
        <!-- Left Section (Navigation) -->
		<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">

			<h2>Approve Company</h2>

				<?php   

						$view = new userAccount();
						$qres = $view->viewAccount();
						
						if($qres){
							$accountsTable = "<table border = 1 class='center'>";
							$accountsTable .= "	<tr>
													<th>Email</th>
													<th>First Name</th>
													<th>Last Name</th>
													<th>Plan ID</th>
													<th>Company Name</th>
													<th>Company UEN</th>
													</tr>\n";
							$accountsTable .= "<br/>";
							}
						while ($Row = $qres->fetch_assoc()) {
							$accountsTable.= "<tr>\n";
							$accountsTable .= "<td>" . $Row['Email'] . "</td>";
							$accountsTable .= "<td>" . $Row['FirstName'] . "</td>";
							$accountsTable .= "<td>" . $Row['LastName'] . "</td>";
							$accountsTable .= "<td>" . $Row['PlanID'] . "</td>";
							$accountsTable .= "<td>" . $Row['CompanyName'] . "</td>";
							$accountsTable .= "<td>" . $Row['CompanyUEN'] . "</td>";
						
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='approveID' value='" . $Row['ApplicationID'] . "'/>
								<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
								<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
								<input type='hidden' name='email' value='" . $Row['Email'] . "'/>
								<input type='hidden' name='password' value='" . $Row['Password'] . "'/>
								<input type='hidden' name='cname' value='" . $Row['CompanyName'] . "'/>
								<input type='hidden' name='companyUEN' value='" . $Row['CompanyUEN'] . "'/>
								<input type='hidden' name='planID' value='" . $Row['PlanID'] . "'/>
								<input type='submit' name='approveAccount' value='Approve'>
								</form></td>";
								
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='applicationID' value='" . $Row['ApplicationID'] . "'/>
								<input type='hidden' name='cname' value='" . $Row['CompanyName'] . "'/>
								<input type='submit' name='deleteEntry' value='Delete'>
								</form></td>";
							$accountsTable.= "</tr>";
						}
						$accountsTable.= "</table>";
						echo $accountsTable;
						echo $_SESSION['message'];
						
						
				?>
        </div>
    </div>

</body>
</html>


