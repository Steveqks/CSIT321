<?php
session_start();

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

			<h2>Approve Users</h2>

				<?php   include_once('superadmin_manageCAdmin_approve_unreg_user_functions.php');

						$view = new viewAccountController();
						$qres = $view->viewAccount();
						
						if($qres){
							$accountsTable = "<table border = 1 class='center'>";
							$accountsTable .= "	<tr>
													<th>Email</th>
													<th>FirstName</th>
													<th>LastName</th>
													<th>PlanID</th>
													<th>CompanyName</th>
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
						
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='approveID' value='" . $Row['ApplicationID'] . "'/>
								<input type='hidden' name='fname' value='" . $Row['FirstName'] . "'/>
								<input type='hidden' name='lname' value='" . $Row['LastName'] . "'/>
								<input type='hidden' name='email' value='" . $Row['Email'] . "'/>
								<input type='hidden' name='password' value='" . $Row['Password'] . "'/>
								<input type='hidden' name='cname' value='" . $Row['CompanyName'] . "'/>
								<input type='hidden' name='planID' value='" . $Row['PlanID'] . "'/>
								<input type='submit' name='approveAccount' value='Approve'>
								</form></td>";
							$accountsTable.= "</tr>";
						}
						$accountsTable.= "</table>";
						echo  $accountsTable;
						
						if(isset($_POST['approveAccount']))
						{
							$aprrove = new approveAccountController();
							
							switch ($aprrove->approveAccount()){
								//company exists
								case 1 : echo "company already exists in system"; break;
								
								//company admin exists
								case 2 : echo "company admin already exists in system"; break;
								
								//create both
								case 3 : echo "company and company admin created."; break;
								default : echo "nothing happened"; break;
							}

						}
				?>
        </div>
    </div>

</body>
</html>

